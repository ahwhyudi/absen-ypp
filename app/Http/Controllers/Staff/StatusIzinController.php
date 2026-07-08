<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;

class StatusIzinController extends Controller
{
    public function index()
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