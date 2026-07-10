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

        $query = Attendance::with('user')
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
