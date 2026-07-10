<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    // Karena jalan jam 02:05 pagi, kita cek tanggal kemarin
    $yesterday = now()->subDay()->toDateString();
    
    // Ambil semua user dengan role Karyawan
    $employees = User::role('Karyawan')->get();

    foreach ($employees as $employee) {
        // Cek apakah karyawan ini sudah punya absen (atau izin) di tanggal kemarin
        $hasAttendance = Attendance::where('user_id', $employee->id)
                                   ->where('date', $yesterday)
                                   ->exists();

        // Kalau kosong, buatin record dengan status 'absent'
        if (!$hasAttendance) {
            Attendance::create([
                'user_id'   => $employee->id,
                'date'      => $yesterday,
                'status_in' => 'absent',
                // Kosongkan yang lain karena dia bolos
            ]);
        }
    }
})->dailyAt('02:05');