<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('check_in', Carbon::today())
            ->first();

        return view('dashboard.staff.index', compact('attendance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'image' => 'required',
            'tipe_absen' => 'required'
        ]);

        $user = auth()->user();

        $attendance = Attendance::firstOrCreate(
            [
                'user_id' => $user->id,
                'date' => today(),
            ]
        );

        /*
    |--------------------------------------------------------------------------
    | Simpan Foto
    |--------------------------------------------------------------------------
    */

        $image = $request->image;

        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);

        $imageName = Str::uuid() . '.jpg';

        Storage::disk('public')->put(
            'attendance/' . $imageName,
            base64_decode($image)
        );

        /*
    |--------------------------------------------------------------------------
    | Absen Masuk
    |--------------------------------------------------------------------------
    */

        if ($request->tipe_absen == 'masuk') {

            $attendance->update([

                'check_in' => now()->format('H:i:s'),

                'latitude_in' => $request->latitude,

                'longitude_in' => $request->longitude,

                'photo_in' => $imageName,

                'ip_address' => $request->ip(),

                'user_agent' => $request->userAgent()

            ]);

            return response()->json([

                'status' => 'sukses',

                'pesan' => 'Absen masuk berhasil.'

            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Absen Pulang
    |--------------------------------------------------------------------------
    */

        $attendance->update([

            'check_out' => now()->format('H:i:s'),

            'latitude_out' => $request->latitude,

            'longitude_out' => $request->longitude,

            'photo_out' => $imageName

        ]);

        return response()->json([

            'status' => 'sukses',

            'pesan' => 'Absen pulang berhasil.'

        ]);
    }

    public function history(Request $request)
    {
        $month = $request->bulan ?? now()->format('Y-m');

        $attendances = Attendance::where('user_id', auth()->id())
            ->whereYear('date', Carbon::parse($month)->year)
            ->whereMonth('date', Carbon::parse($month)->month)
            ->latest('date')
            ->get();

        $stat = [
            'total_hadir' => $attendances->count(),

            'tepat_waktu' => $attendances
                ->where('status_in', 'present')
                ->count(),

            'terlambat' => $attendances
                ->where('status_in', 'late')
                ->count(),

            'sudah_pulang' => $attendances
                ->whereNotNull('check_out')
                ->count(),
        ];

        return view('dashboard.staff.riwayat.index', [
            'attendances' => $attendances,
            'stat' => $stat,
            'filter_bulan' => $month,
        ]);
    }

    public function leave()
    {
        return view('dashboard.staff.cuti.index');
    }

    public function leaveStatus()
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
