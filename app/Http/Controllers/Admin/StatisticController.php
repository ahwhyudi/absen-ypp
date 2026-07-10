<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        // Set default bulan ke bulan ini jika tidak ada input
        $filter_bulan = $request->input('bulan', date('Y-m'));

        // Pecah string 'YYYY-MM' untuk query database
        $year = substr($filter_bulan, 0, 4);
        $month = substr($filter_bulan, 5, 2);

        // Ambil semua user KECUALI admin, beserta data absensi dan izin di bulan tersebut
        $users = User::whereDoesntHave('roles', function($q) {
            $q->where('name', 'admin'); // Sesuaikan jika role admin lu namanya beda
        })->with([
            'attendances' => function($query) use ($year, $month) {
                $query->whereYear('date', $year)
                      ->whereMonth('date', $month);
            },
            'leaveRequests' => function($query) use ($year, $month) {
                // Sesuai migration: 'approved' dan 'start_date'
                $query->where('status', 'approved')
                      ->whereYear('start_date', $year)
                      ->whereMonth('start_date', $month);
            }
        ])->get();

        $statistics = [];
        $labels = [];
        $dataHadir = [];
        $dataTerlambat = [];

        foreach ($users as $user) {
            $totalHadir = $user->attendances->count();
            
            // Hitung Terlambat (Bisa pakai status_in == 'late' atau cek jam)
            $terlambat = $user->attendances->filter(function($att) {
                return $att->status_in === 'late'; 
                // Jika pakai jam: return Carbon::parse($att->check_in)->format('H:i:s') > '08:00:00';
            })->count();

            $tepatWaktu = $totalHadir - $terlambat;
            
            // Hitung Tidak Pulang
            $tidakPulang = $user->attendances->filter(function($att) {
                return is_null($att->check_out);
            })->count();

            // Total Izin Disetujui
            $totalIzin = $user->leaveRequests->count();

            // Skor Disiplin
            $totalCalc = max($totalHadir, 1); // Hindari division by zero
            $skor = round(($tepatWaktu / $totalCalc) * 100);
            
            $skorColor = match(true) {
                $skor >= 80 => 'text-emerald-600',
                $skor >= 50 => 'text-amber-600',
                default     => 'text-rose-600',
            };

            $statistics[] = [
                'nama'         => $user->name,
                'total_hadir'  => $totalHadir,
                'tepat_waktu'  => $tepatWaktu,
                'terlambat'    => $terlambat,
                'tidak_pulang' => $tidakPulang,
                'total_izin'   => $totalIzin,
                'skor'         => $skor,
                'skor_color'   => $skorColor
            ];
        }

        // Urutkan array: Terlambat terbanyak dulu, lalu Hadir terbanyak (seperti query asli lu)
        usort($statistics, function($a, $b) {
            if ($a['terlambat'] == $b['terlambat']) {
                return $b['total_hadir'] <=> $a['total_hadir'];
            }
            return $b['terlambat'] <=> $a['terlambat'];
        });

        // Pisahkan data untuk Chart.js setelah diurutkan
        foreach ($statistics as $stat) {
            $labels[] = $stat['nama'];
            $dataHadir[] = $stat['total_hadir'];
            $dataTerlambat[] = $stat['terlambat'];
        }

        return view('dashboard.admin.statistik', compact(
            'filter_bulan', 'statistics', 'labels', 'dataHadir', 'dataTerlambat'
        ));
    }
}