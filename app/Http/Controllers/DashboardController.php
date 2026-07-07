<?php

namespace App\Http\Controllers;

use App\Models\Attendence;
use App\Models\LeaveRequests;
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

        $todayAttendance = Attendence::whereDate('date', $today)->count();

        $todayLate = Attendence::whereDate('date', $today)
            ->where('status_in', 'late')
            ->count();

        $pendingLeave = LeaveRequests::where('status', 'pending')->count();

        $approvedLeave = LeaveRequests::where('status', 'approved')->count();

        /*
        |--------------------------------------------------------------------------
        | Attendence Terbaru
        |--------------------------------------------------------------------------
        */

        $latestAttendences = Attendence::with('user')
            ->latest('date')
            ->latest('check_in')
            ->take(10)
            ->get();

        return view('dashboard.index', compact(
            'totalEmployee',
            'todayAttendence',
            'todayLate',
            'pendingLeave',
            'approvedLeave',
            'latestAttendences'
        ));
    }
}