<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        'pending_leave' => LeaveRequest::where('status','pending')->count(),

        'staff' => User::role('employee')->count(),
    ];

    return view('dashboard.admin.dashboard', compact(
        'attendances',
        'stats',
        'month'
    ));
    }
}