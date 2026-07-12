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
        $user = auth()->user();
        $now = now();

        // 1. Cek apakah ada absen KEMARIN yang belum pulang (untuk yang lembur dini hari)
        $yesterdayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $now->copy()->subDay()->toDateString())
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->first();

        // 2. Jika ada absen kemarin yang menggantung, tampilkan itu di layar!
        if ($yesterdayAttendance) {
            $attendance = $yesterdayAttendance;
        } else {
            // 3. Jika tidak ada, cari data absensi berdasarkan logicalDate hari ini
            $logicalDate = ($now->hour < 2)
                ? $now->copy()->subDay()->toDateString()
                : $now->toDateString();

            $attendance = Attendance::where('user_id', $user->id)
                ->where('date', $logicalDate)
                ->first();
        }

        return view('dashboard.staff.index', compact('attendance'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'latitude'   => 'required',
            'longitude'  => 'required',
            'image'      => 'required',
            'tipe_absen' => 'required|in:masuk,pulang'
        ]);

        $user = auth()->user();
        $now = now();
        $currentTime = $now->format('H:i:s');

        // 1. Cek dulu, apakah user punya absen MASUK kemarin yang BELUM PULANG?
        // (Berguna kalau user lembur sampai lewat jam 2 pagi)
        $yesterdayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $now->copy()->subDay()->toDateString())
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->first();

        // 2. Tentukan mau pakai data absen yang mana
        if ($request->tipe_absen == 'pulang' && $yesterdayAttendance) {
            // Kalau dia mau absen pulang dan ternyata absen kemarin belum ditutup, 
            // maka gabungkan ke absen kemarin walau sudah jam 3 / jam 4 pagi!
            $attendance = $yesterdayAttendance;
        } else {
            // Jika tidak ada tanggungan absen kemarin, gunakan aturan normal (cut-off jam 02:00)
            $logicalDate = ($now->hour < 2)
                ? $now->copy()->subDay()->toDateString()
                : $now->toDateString();

            $attendance = Attendance::firstOrCreate([
                'user_id' => $user->id,
                'date'    => $logicalDate,
            ]);
        }

        // ==========================================
        // ABSEN MASUK
        // ==========================================
        if ($request->tipe_absen == 'masuk') {

            // 1. Validasi DULU sebelum proses gambar!
            if ($attendance->check_in !== null) {
                return response()->json([
                    'status' => 'error',
                    'pesan'  => 'Anda sudah melakukan absen masuk untuk sesi hari ini.'
                ], 400);
            }

            // 2. Upload Gambar setelah dipastikan boleh absen
            $imageName = $this->uploadAttendanceImage($request->image);

            // 3. Logika Terlambat (Di atas 10:00 = late)
            $statusIn = ($currentTime > '10:00:00') ? 'late' : 'present';

            $attendance->update([
                'check_in'     => $currentTime,
                'status_in'    => $statusIn,
                'latitude_in'  => $request->latitude,
                'longitude_in' => $request->longitude,
                'photo_in'     => $imageName,
                'ip_address'   => $request->ip(),
                'user_agent'   => $request->userAgent()
            ]);

            return response()->json([
                'status' => 'sukses',
                'pesan'  => 'Absen masuk berhasil (' . $statusIn . ').'
            ]);
        }

        // ==========================================
        // ABSEN PULANG
        // ==========================================
        if ($request->tipe_absen == 'pulang') {

            // 1. Validasi DULU sebelum proses gambar!
            if ($attendance->check_in === null) {
                return response()->json([
                    'status' => 'error',
                    'pesan'  => 'Anda harus absen masuk terlebih dahulu!'
                ], 400);
            }

            if ($attendance->check_out !== null) {
                return response()->json([
                    'status' => 'error',
                    'pesan'  => 'Anda sudah melakukan absen pulang untuk sesi hari ini.'
                ], 400);
            }

            // 2. Upload Gambar setelah dipastikan boleh absen
            $imageName = $this->uploadAttendanceImage($request->image);

            // 3. Logika Pulang Cepat
            $statusOut = ($currentTime < '17:00:00') ? 'early_leave' : 'on_time';

            $attendance->update([
                'check_out'     => $currentTime,
                'status_out'    => $statusOut,
                'latitude_out'  => $request->latitude,
                'longitude_out' => $request->longitude,
                'photo_out'     => $imageName,
                'note_out'      => $request->note_out,
            ]);

            return response()->json([
                'status' => 'sukses',
                'pesan'  => 'Absen pulang berhasil.'
            ]);
        }
    }

    /**
     * Helper function untuk upload gambar supaya kode lebih bersih
     */
    private function uploadAttendanceImage($base64Image)
    {
        $image = str_replace('data:image/jpeg;base64,', '', $base64Image);
        $image = str_replace(' ', '+', $image);
        $imageName = Str::uuid() . '.jpg';

        Storage::disk('public')->put('attendance/' . $imageName, base64_decode($image));

        return $imageName;
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
