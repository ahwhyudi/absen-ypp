<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('check_in', Carbon::today())
            ->first();

        return view('dashboard.staff.index', compact('attendance'));
    }

    // 
    public function store(Request $request)
    {
        $request->validate([
            'latitude'   => 'required',
            'longitude'  => 'required',
            'image'      => 'required',
            'tipe_absen' => 'required|in:masuk,pulang'
        ]);

        $user = auth()->user();
        $now = now(); // Pastikan timezone app.php sudah 'Asia/Jakarta'

        // ==========================================
        // LOGIKA RESET JAM 2 PAGI (SHIFT CUT-OFF)
        // ==========================================
        // Kita kurangi waktu sekarang sebanyak 2 jam. 
        // Jadi kalau staf absen jam 01:30 pagi (tanggal 2), sistem masih menganggap itu absen untuk tanggal 1.
        $logicalDate = $now->copy()->subHours(2)->toDateString();
        $currentTime = $now->format('H:i:s');

        $attendance = Attendance::firstOrCreate([
            'user_id' => $user->id,
            'date'    => $logicalDate,
        ]);

        // --- Proses Gambar ---
        $image = $request->image;
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = Str::uuid() . '.jpg';
        
        Storage::disk('public')->put('attendance/' . $imageName, base64_decode($image));

        // ==========================================
        // ABSEN MASUK
        // ==========================================
        if ($request->tipe_absen == 'masuk') {
            
            // Validasi: Cuma boleh 1x absen masuk
            if ($attendance->check_in !== null) {
                return response()->json([
                    'status' => 'error',
                    'pesan'  => 'Anda sudah melakukan absen masuk hari ini.'
                ], 400); // Bad Request
            }

            // Logika Terlambat (Di atas 08:00 = late)
            $statusIn = ($currentTime > '08:00:00') ? 'late' : 'present';

            $attendance->update([
                'check_in'     => $currentTime,
                'status_in'    => $statusIn,
                'latitude_in'  => $request->latitude,
                'longitude_in' => $request->longitude,
                'photo_in'     => $imageName,
                'ip_address'   => $request->ip(),
                'user_agent'   => $request->userAgent()
            ]);

            return response()->json([
                'status' => 'sukses',
                'pesan'  => 'Absen masuk berhasil (' . $statusIn . ').'
            ]);
        }

        // ==========================================
        // ABSEN PULANG
        // ==========================================
        if ($request->tipe_absen == 'pulang') {
            
            // Validasi: Harus absen masuk dulu sebelum bisa pulang
            if ($attendance->check_in === null) {
                return response()->json([
                    'status' => 'error',
                    'pesan'  => 'Anda harus absen masuk terlebih dahulu!'
                ], 400);
            }

            // Validasi: Cuma boleh 1x absen pulang
            if ($attendance->check_out !== null) {
                return response()->json([
                    'status' => 'error',
                    'pesan'  => 'Anda sudah melakukan absen pulang hari ini.'
                ], 400);
            }

            // Logika Pulang Cepat (Di bawah 17:00 = early_leave, Sisanya = on_time/null)
            // Lu bisa ganti 'on_time' jadi null kalau di database boleh null
            $statusOut = ($currentTime < '17:00:00') ? 'early_leave' : 'on_time';

            $attendance->update([
                'check_out'     => $currentTime,
                'status_out'    => $statusOut,
                'latitude_out'  => $request->latitude,
                'longitude_out' => $request->longitude,
                'photo_out'     => $imageName,
                'note_out'      => $request->note_out, // Optional: Catatan pulang
            ]);

            return response()->json([
                'status' => 'sukses',
                'pesan'  => 'Absen pulang berhasil.'
            ]);
        }
    }
    public function history(Request $request)
    {
        $month = $request->bulan ?? now()->format('Y-m');

        $attendances = Attendance::where('user_id', auth()->id())
            ->whereYear('date', Carbon::parse($month)->year)
            ->whereMonth('date', Carbon::parse($month)->month)
            ->latest('date')
            ->get();

        $stat = [
            'total_hadir' => $attendances->count(),

            'tepat_waktu' => $attendances
                ->where('status_in', 'present')
                ->count(),

            'terlambat' => $attendances
                ->where('status_in', 'late')
                ->count(),

            'sudah_pulang' => $attendances
                ->whereNotNull('check_out')
                ->count(),
        ];

        return view('dashboard.staff.riwayat.index', [
            'attendances' => $attendances,
            'stat' => $stat,
            'filter_bulan' => $month,
        ]);
    }

    public function leave()
    {
        return view('dashboard.staff.cuti.index');
    }

    public function leaveStatus()
    {
        $leaveRequests = LeaveRequest::where('user_id', auth()->id())
            ->latest()
            ->get();

        $pendingCount = $leaveRequests
            ->where('status', 'pending')
            ->count();

        return view('dashboard.staff.status-izin.index', [
            'leaveRequests' => $leaveRequests,
            'pendingCount' => $pendingCount,
        ]);
    }
}
