<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */

        $totalEmployee = User::role('Karyawan')->count();

        $todayAttendance = Attendance::whereDate('date', $today)->count();

        $todayLate = Attendance::whereDate('date', $today)
            ->where('status_in', 'late')
            ->count();

        $pendingLeave = LeaveRequest::where('status', 'pending')->count();

        $approvedLeave = LeaveRequest::where('status', 'approved')->count();

        /*
        |--------------------------------------------------------------------------
        | Attendence Terbaru
        |--------------------------------------------------------------------------
        */

        $latestAttendences = Attendance::with('user')
            ->latest('date')
            ->latest('check_in')
            ->take(10)
            ->get();

        return view('dashboard.index', compact(
            'totalEmployee',
            'todayAttendance',
            'todayLate',
            'pendingLeave',
            'approvedLeave',
            'latestAttendences'
        ));
    }
}