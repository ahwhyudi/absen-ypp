<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->filter_bulan ?? now()->format('Y-m');

        // PASTIKAN memuat relasi roles jika pakai Spatie, atau cukup 'user' jika ada kolom jabatan/role di tabel users
        $query = Attendance::with(['user', 'user.roles'])
            ->whereYear('date', Carbon::parse($month)->year)
            ->whereMonth('date', Carbon::parse($month)->month);

        $attendances = $query
            ->latest('date')
            ->paginate(15);

        $stats = [
            'total_presensi' => $query->count(),
            'pending_leave' => LeaveRequest::where('status', 'pending')->count(),
            'staff' => User::role('employee')->count(),
        ];

        return view('dashboard.admin.dashboard', compact(
            'attendances',
            'stats',
            'month'
        ));
    }

    // ==========================================
    // UPDATE DATA ABSENSI BY ADMIN
    // ==========================================
    // ==========================================
    // UPDATE DATA ABSENSI BY ADMIN (AMANDEMEN)
    // ==========================================
    public function update(Request $request, $id)
    {
        $request->validate([
            'check_in'      => 'nullable|date_format:H:i:s,H:i',
            'check_out'     => 'nullable|date_format:H:i:s,H:i',
            'latitude_in'   => 'nullable|numeric',
            'longitude_in'  => 'nullable|numeric',
            'latitude_out'  => 'nullable|numeric',
            'longitude_out' => 'nullable|numeric',
        ]);

        $attendance = Attendance::findOrFail($id);

        // 1. Cek apakah check_in diubah? Kalau ya, hitung ulang status terlambat/tepat waktunya
        $checkInTime = $request->filled('check_in')
            ? Carbon::parse($request->check_in)->format('H:i:s')
            : $attendance->check_in; // <-- Kalau kosong, pakai data lama!

        $statusIn = ($checkInTime && $checkInTime > '10:00:00') ? 'late' : 'present';

        // 2. Cek apakah check_out diubah? Kalau ya, hitung ulang status pulang cepatnya
        $checkOutTime = $request->filled('check_out')
            ? Carbon::parse($request->check_out)->format('H:i:s')
            : $attendance->check_out; // <-- Kalau kosong, pakai data lama!

        $statusOut = ($checkOutTime && $checkOutTime < '17:00:00') ? 'early_leave' : ($checkOutTime ? 'on_time' : null);

        // 3. Update database dengan aman tanpa menghapus data lama
        $attendance->update([
            'check_in'      => $checkInTime,
            'status_in'     => $statusIn,
            'check_out'     => $checkOutTime,
            'status_out'    => $statusOut,
            'latitude_in'   => $request->filled('latitude_in') ? $request->latitude_in : $attendance->latitude_in,
            'longitude_in'  => $request->filled('longitude_in') ? $request->longitude_in : $attendance->longitude_in,
            'latitude_out'  => $request->filled('latitude_out') ? $request->latitude_out : $attendance->latitude_out,
            'longitude_out' => $request->filled('longitude_out') ? $request->longitude_out : $attendance->longitude_out,
        ]);

        return redirect()->back()->with('success', 'Data absensi berhasil diperbarui!');
    }

    // ==========================================
    // HAPUS DATA ABSENSI BY ADMIN
    // ==========================================
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);

        // (Opsional) Hapus foto dari storage jika ingin menghemat memori server
        // if ($attendance->photo_in) Storage::disk('public')->delete('attendance/' . $attendance->photo_in);
        // if ($attendance->photo_out) Storage::disk('public')->delete('attendance/' . $attendance->photo_out);

        $attendance->delete();

        return redirect()->back()->with('success', 'Data absensi berhasil dihapus!');
    }

    public function exportExcel(Request $request)
    {
        // Ambil bulan dari parameter URL (misal: ?bulan=2026-07)
        // Kalau kosong, default ke bulan sekarang
        $month = $request->bulan ?? now()->format('Y-m');

        // Bikin nama file dinamis biar rapi
        $fileName = 'Laporan_Presensi_' . $month . '.xlsx';

        return Excel::download(new AttendanceExport($month), $fileName);
    }
}
