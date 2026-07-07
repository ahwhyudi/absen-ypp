<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    public function index()
    {
        return view('dashboard.staff.cuti.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_izin' => 'required|in:Izin,Sakit,Cuti',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required',
            'dokumen_pendukung' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $document = null;

        if ($request->hasFile('dokumen_pendukung')) {
            $document = $request->file('dokumen_pendukung')
                ->store('leave-documents', 'public');
        }

        LeaveRequest::create([
            'user_id' => auth()->id(),

            'type' => match ($request->jenis_izin) {
                'Izin' => 'permission',
                'Sakit' => 'sick',
                'Cuti' => 'leave',
            },

            'start_date' => $request->tanggal_mulai,
            'end_date' => $request->tanggal_selesai,
            'reason' => $request->keterangan,
            'document_path' => $document,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('leave-request.index')
            ->with('success', 'Pengajuan berhasil dikirim.');
    }
}