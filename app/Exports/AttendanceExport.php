<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $month;

    // Terima data filter bulan dari Controller
    public function __construct($month)
    {
        $this->month = $month;
    }

    public function collection()
    {
        $date = Carbon::parse($this->month);
        
        // Ambil data sesuai bulan yang difilter
        return Attendance::with('user')
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->latest('date')
            ->get();
    }

    // Nama-nama Header Kolom Excel
    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Status Masuk',
            'Status Pulang',
            'Koordinat Masuk',
            'Koordinat Pulang',
            'Laporan Pekerjaan (Note Out)',
            'Link Foto Masuk',
            'Link Foto Pulang',
            'User Agent (Device/Browser)'
        ];
    }

    // Mapping isi data per barisnya
    public function map($row): array
    {
        // Bikin URL lengkap untuk foto agar bisa diklik langsung dari Excel
        $fotoMasuk = $row->photo_in ? asset('storage/attendance/' . $row->photo_in) : '-';
        $fotoPulang = $row->photo_out ? asset('storage/attendance/' . $row->photo_out) : '-';

        // Gabung latitude dan longitude
        $koordinatMasuk = $row->latitude_in ? $row->latitude_in . ', ' . $row->longitude_in : '-';
        $koordinatPulang = $row->latitude_out ? $row->latitude_out . ', ' . $row->longitude_out : '-';

        return [
            $row->user->name ?? 'User Dihapus',
            Carbon::parse($row->date)->format('d M Y'),
            $row->check_in ?? '-',
            $row->check_out ?? '-',
            strtoupper($row->status_in ?? '-'),
            strtoupper($row->status_out ?? '-'),
            $koordinatMasuk,
            $koordinatPulang,
            $row->note_out ?? '-',
            $fotoMasuk,
            $fotoPulang,
            $row->user_agent ?? '-'
        ];
    }
}