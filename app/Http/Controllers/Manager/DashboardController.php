<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil data pengajuan cuti/izin yang statusnya masih 'pending'
        // Menggunakan eager loading 'user' agar bisa mengambil nama karyawan
        $log_cuti = LeaveRequest::with('user')
                                 ->where('status', 'pending')
                                 ->orderBy('created_at', 'asc')
                                 ->get();
        // 2. Ambil data absensi 50 terakhir milik user dengan role 'employee'
        // Karena ini manager, asumsikan dia melihat semua absensi employee (karyawan)
        $log_tim = Attendance::with('user')
                             ->whereHas('user.roles', function($q){
                                 $q->where('name', 'employee'); // Sesuaikan nama role karyawan
                             })
                             ->orderBy('date', 'desc')
                             ->orderBy('check_in', 'desc')
                             ->limit(50)
                             ->get();

        return view('dashboard.manager.index', compact('log_cuti', 'log_tim'));
    }
}