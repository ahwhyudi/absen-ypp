<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Attendence;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendenceController extends Controller
{
    /**
     * Halaman utama absensi
     */
    public function index()
    {
        $user = Auth::user();

        $today = Carbon::today()->toDateString();

        $attendance = Attendence::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $status = [
            'text' => 'Belum Mengirim Data',
            'color' => 'bg-gray-100 text-gray-600',
            'type' => 'check_in',
            'button' => 'Kirim Absen Masuk Sekarang'
        ];

        if ($attendance) {

            // Sudah check in tetapi belum check out
            if ($attendance->check_in && !$attendance->check_out) {

                $status = [
                    'text' => 'Sudah Absen Masuk (' . $attendance->check_in . ')',
                    'color' => 'bg-emerald-100 text-emerald-700',
                    'type' => 'check_out',
                    'button' => 'Kirim Absen Pulang Sekarang'
                ];
            }

            // Sudah check in & check out
            if ($attendance->check_in && $attendance->check_out) {

                $status = [
                    'text' => 'Selesai Kerja',
                    'color' => 'bg-blue-100 text-blue-700',
                    'type' => 'finished',
                    'button' => 'Sudah Absen'
                ];
            }
        }

        return view('pages.attendance.index', [
            'user' => $user,
            'attendance' => $attendance,
            'status' => $status
        ]);
    }
}