<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index()
    {
        // Menggunakan latest() otomatis mengambil berdasarkan created_at
        $leaveRequests = LeaveRequest::with('user')->latest()->get();
        
        // Sesuaikan dengan enum 'pending'
        $pendingCount = LeaveRequest::where('status', 'pending')->count();

        return view('dashboard.admin.approve-izin', compact('leaveRequests', 'pendingCount'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'tindakan' => 'required|in:setuju,tolak'
        ]);

        // Mapping tindakan ke enum status di database
        $status_baru = ($request->tindakan === 'setuju') ? 'approved' : 'rejected';

        // Update data, dan manfaatkan kolom approved_by untuk audit trail!
        $leaveRequest->update([
            'status'      => $status_baru,
            'approved_by' => Auth::id(), 
        ]);

        $pesan = ($status_baru === 'approved') ? 'Disetujui' : 'Ditolak';

        return redirect()->route('admin.izin.index')
                         ->with('success', 'Status pengajuan izin berhasil ' . $pesan);
    }
}