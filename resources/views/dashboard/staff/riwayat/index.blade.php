<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Absensi Saya - YPP</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gray-50 font-sans text-gray-800 antialiased">

    @include('includes.components.header')

    <main class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">

        <!-- Judul + Filter -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">Riwayat Absensi Saya</h1>
                <p class="text-xs text-gray-400 mt-0.5">Rekap kehadiran pribadi per bulan</p>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-gray-500 uppercase">Filter Bulan:</label>
                <form method="GET">
                    <input type="month" name="bulan" value="{{ $filter_bulan }}" onchange="this.form.submit()"
                        class="bg-white border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500">
                </form>
            </div>
        </div>

        <!-- Kartu Statistik -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm text-center">
                <p class="text-2xl font-extrabold text-gray-900">{{ $stat['total_hadir'] }}</p>
                <p class="text-xs text-gray-400 mt-1 font-medium">Hari Hadir</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-emerald-100 shadow-sm text-center">
                <p class="text-2xl font-extrabold text-emerald-600">{{ $stat['tepat_waktu'] }}</p>
                <p class="text-xs text-gray-400 mt-1 font-medium">Tepat Waktu</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-rose-100 shadow-sm text-center">
                <p class="text-2xl font-extrabold text-rose-500">{{ $stat['terlambat'] }}</p>
                <p class="text-xs text-gray-400 mt-1 font-medium">Terlambat</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-blue-100 shadow-sm text-center">
                <p class="text-2xl font-extrabold text-blue-500">{{ $stat['sudah_pulang'] }}</p>
                <p class="text-xs text-gray-400 mt-1 font-medium">Lengkap (Masuk+Pulang)</p>
            </div>
        </div>

        <!-- Tabel Riwayat -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-wider">
                            <th class="px-5 py-4">Tanggal</th>
                            <th class="px-5 py-4">Jam Masuk</th>
                            <th class="px-5 py-4">Jam Pulang</th>
                            <th class="px-5 py-4 text-center">Status</th>
                            <th class="px-5 py-4">Lokasi Masuk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">

                        @forelse($attendances as $attendance)
                            <tr class="hover:bg-gray-50 transition">

                                {{-- Tanggal --}}
                                <td class="px-5 py-4 font-semibold text-gray-900">
                                    {{ $attendance->date->translatedFormat('D, d M Y') }}
                                </td>

                                {{-- Jam Masuk --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">

                                        <span
                                            class="font-mono font-semibold
                        {{ $attendance->status_in == 'late' ? 'text-rose-600' : 'text-emerald-600' }}">

                                            {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}
                                            WIB
                                        </span>

                                        @if ($attendance->status_in == 'late')
                                            <span
                                                class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[10px] font-bold">
                                                TERLAMBAT
                                            </span>
                                        @endif

                                    </div>
                                </td>

                                {{-- Jam Pulang --}}
                                <td class="px-5 py-4 font-mono text-gray-600">

                                    @if ($attendance->check_out)
                                        {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }} WIB
                                    @else
                                        <span class="italic text-amber-500 text-xs">
                                            Belum Absen
                                        </span>
                                    @endif

                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 text-center">

                                    @if (!$attendance->check_out)
                                        <span
                                            class="px-2 py-1 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold uppercase">
                                            Setengah
                                        </span>
                                    @elseif($attendance->status_in == 'late')
                                        <span
                                            class="px-2 py-1 rounded-full bg-rose-100 text-rose-700 text-[10px] font-bold uppercase">
                                            Terlambat
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase">
                                            Tepat Waktu
                                        </span>
                                    @endif

                                </td>

                                {{-- Lokasi --}}
                                <td class="px-5 py-4">

                                    @if ($attendance->latitude_in)
                                        <a href="https://www.google.com/maps?q={{ $attendance->latitude_in }},{{ $attendance->longitude_in }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1 text-xs text-amber-600 hover:text-amber-700 hover:underline">

                                            <i data-lucide="map-pin" class="w-3 h-3"></i>

                                            {{ number_format($attendance->latitude_in, 4) }},
                                            {{ number_format($attendance->longitude_in, 4) }}

                                        </a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="px-6 py-12 text-center">

                                    <i data-lucide="calendar-x" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>

                                    <p class="text-gray-400">
                                        Tidak ada data absensi untuk bulan ini.6
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bottom nav mobile -->

    </main>
    @include('includes.components.navbar')

    <script>
        lucide.createIcons();
    </script>
</body>

</html>
