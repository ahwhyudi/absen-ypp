@extends('dashboard.admin.index')

@section('content')
    <div class="space-y-6 pt-16 lg:pt-0 max-w-[1600px] mx-auto antialiased">

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
            <div
                class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm flex items-center gap-4 sm:gap-5 hover:shadow-md hover:border-gray-200 transition-all duration-200 group">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-50 rounded-xl sm:rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-emerald-100 transition-colors">
                    <i data-lucide="clock-check" class="w-6 h-6 sm:w-7 sm:h-7 text-emerald-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight truncate">
                        {{ $stats['total_presensi'] }}
                    </p>
                    <p class="text-xs sm:text-sm text-gray-400 font-medium mt-0.5 sm:mt-1 truncate">Total Presensi (Filter
                        Aktif)</p>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm flex items-center gap-4 sm:gap-5 hover:shadow-md hover:border-gray-200 transition-all duration-200 group">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 bg-amber-50 rounded-xl sm:rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-amber-100 transition-colors">
                    <i data-lucide="mail" class="w-6 h-6 sm:w-7 sm:h-7 text-amber-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight truncate">
                        {{ $stats['pending_leave'] }}
                    </p>
                    <div class="flex flex-wrap items-center gap-2 mt-0.5 sm:mt-1">
                        <p class="text-xs sm:text-sm text-gray-400 font-medium truncate">Menunggu Izin</p>
                        @if ($stats['pending_leave'] > 0)
                            <span
                                class="text-[10px] text-amber-700 font-bold bg-amber-100 px-2 py-0.5 rounded-full animate-pulse whitespace-nowrap">
                                Perlu Diproses
                            </span>
                        @else
                            <span
                                class="text-[10px] text-emerald-700 font-medium bg-emerald-50 px-2 py-0.5 rounded-full whitespace-nowrap">
                                Semua Beres ✓
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm flex items-center gap-4 sm:gap-5 hover:shadow-md hover:border-gray-200 transition-all duration-200 group sm:col-span-2 xl:col-span-1">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-50 rounded-xl sm:rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-blue-100 transition-colors">
                    <i data-lucide="users" class="w-6 h-6 sm:w-7 sm:h-7 text-blue-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight truncate">{{ $stats['staff'] }}
                    </p>
                    <p class="text-xs sm:text-sm text-gray-400 font-medium mt-0.5 sm:mt-1 truncate">Total Staff Terdaftar
                    </p>
                </div>
            </div>
        </div>

        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 sm:p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div class="space-y-0.5">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
                    <i data-lucide="calendar-range" class="w-5 h-5 text-gray-400"></i> Log Riwayat Presensi
                </h2>
                <p class="text-xs sm:text-sm text-gray-400">Seluruh data koordinat geotagging, peta wilayah, dan foto
                    verifikasi.</p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <form class="w-full sm:w-auto">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <input type="month" name="filter_bulan" value="{{ $month }}" onchange="this.form.submit()"
                            class="bg-gray-50 border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 block w-full sm:w-[180px] ps-10 p-3 transition-all cursor-pointer hover:bg-gray-100 outline-none">
                    </div>
                </form>

                <a href="
                {{ route('admin.attendance.export', ['bulan' => $month]) }}
                    "
                    class="w-full sm:w-auto bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 font-bold py-3 px-4 rounded-xl text-sm transition-all flex items-center justify-center gap-2 whitespace-nowrap shadow-sm">
                    <i data-lucide="sheet" class="w-4 h-4"></i> Export Excel
                </a>
            </div>
        </div>

        {{-- TAMPILAN MOBILE (Hanya muncul di layar < 1024px) --}}
        <div class="block lg:hidden space-y-4">
            @forelse($attendances as $attendance)
                @php
                    $terlambat = $attendance->status_in === 'late';
                    $belumPulang = is_null($attendance->check_out);
                @endphp
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-50 pb-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                class="w-9 h-9 rounded-full bg-gradient-to-tr from-amber-50 to-orange-100 flex items-center justify-center text-amber-700 font-bold text-xs shrink-0">
                                {{ strtoupper(substr($attendance->user->name, 0, 1)) }}
                            </div>
                            <span class="font-bold text-gray-900 text-sm truncate">{{ $attendance->user->name }}</span>
                        </div>
                        <span class="text-xs text-gray-400 font-medium shrink-0 bg-gray-50 px-2 py-1 rounded-md">
                            {{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100/50">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Jam
                                Masuk</span>
                            @if ($attendance->check_in)
                                <span
                                    class="{{ $terlambat ? 'text-rose-600' : 'text-emerald-600' }} font-bold text-sm block">
                                    {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }} WIB
                                </span>
                                @if ($terlambat)
                                    <span
                                        class="inline-block mt-1 px-2 py-0.5 text-[9px] bg-rose-100 text-rose-700 font-black rounded-md uppercase tracking-wider scale-90">Telat</span>
                                @endif
                            @else
                                <span class="text-gray-400 text-sm font-medium block">-</span>
                            @endif
                        </div>

                        <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100/50">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Jam
                                Pulang</span>
                            @if ($attendance->check_out)
                                <span class="font-bold text-amber-600 text-sm block">
                                    {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }} WIB
                                </span>
                            @else
                                <span
                                    class="text-gray-400 font-medium text-xs block bg-gray-100 py-0.5 rounded-md mt-0.5">Belum
                                    Pulang</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 text-xs">
                        <div class="flex gap-2">
                            @if ($attendance->latitude_in)
                                <a href="http://maps.google.com/maps?q={{ $attendance->latitude_in }},{{ $attendance->longitude_in }}"
                                    target="_blank"
                                    class="flex items-center gap-1.5 text-blue-600 font-semibold bg-blue-50 px-3 py-1.5 rounded-xl hover:bg-blue-100 transition-colors">
                                    <i data-lucide="map" class="w-3.5 h-3.5"></i> Maps In
                                </a>
                            @endif
                            @if ($attendance->latitude_out)
                                <a href="http://maps.google.com/maps?q={{ $attendance->latitude_out }},{{ $attendance->longitude_out }}"
                                    target="_blank"
                                    class="flex items-center gap-1.5 text-indigo-600 font-semibold bg-indigo-50 px-3 py-1.5 rounded-xl hover:bg-indigo-100 transition-colors">
                                    <i data-lucide="map" class="w-3.5 h-3.5"></i> Maps Out
                                </a>
                            @endif
                        </div>

                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] text-gray-400 font-bold uppercase mr-1">Foto:</span>
                            <div
                                class="w-7 h-7 rounded-md overflow-hidden border bg-gray-50 flex items-center justify-center">
                                @if ($attendance->photo_in)
                                    <img src="{{ asset('storage/' . $attendance->photo_in) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <i data-lucide="image-off" class="w-3 h-3 text-gray-300"></i>
                                @endif
                            </div>
                            <div
                                class="w-7 h-7 rounded-md overflow-hidden border bg-gray-50 flex items-center justify-center">
                                @if ($attendance->photo_out)
                                    <img src="{{ asset('storage/' . $attendance->photo_out) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <i data-lucide="image-off" class="w-3 h-3 text-gray-300"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-10 border border-gray-100 text-center text-gray-400">
                    <i data-lucide="calendar-x" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                    <p class="text-sm font-medium">Tidak ada data presensi.</p>
                </div>
            @endforelse
        </div>

        {{-- TAMPILAN DESKTOP (Hanya muncul di layar >= 1024px) --}}
        <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50/70 text-xs font-bold uppercase tracking-wider text-gray-500 border-b border-gray-100">
                            <th class="px-6 py-5">Nama Staff</th>
                            <th class="px-6 py-5">Tanggal</th>
                            <th class="px-6 py-5">Jam Masuk</th>
                            <th class="px-6 py-5">Jam Pulang</th>
                            <th class="px-6 py-5">Lokasi GPS</th>
                            <th class="px-6 py-5 text-center">Foto Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($attendances as $attendance)
                            @php
                                $terlambat = $attendance->status_in === 'late';
                                $belumPulang = is_null($attendance->check_out);
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-all duration-200 group">

                                <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-full bg-gradient-to-tr from-amber-100 to-orange-50 flex items-center justify-center text-amber-700 font-extrabold text-xs shrink-0 ring-2 ring-white shadow-sm">
                                            {{ strtoupper(substr($attendance->user->name, 0, 1)) }}
                                        </div>
                                        <span
                                            class="group-hover:text-amber-600 transition-colors cursor-pointer">{{ $attendance->user->name }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-gray-500 font-medium whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($attendance->check_in)
                                        <div class="flex items-center gap-2.5">
                                            <span
                                                class="{{ $terlambat ? 'bg-rose-50 text-rose-700 ring-rose-600/20' : 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' }} font-bold px-2.5 py-1 rounded-md ring-1 inset-ring text-xs tracking-wide">
                                                {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }} WIB
                                            </span>

                                            @if ($terlambat)
                                                <span
                                                    class="px-2 py-0.5 rounded-md text-[10px] bg-rose-500 text-white font-bold uppercase tracking-widest shadow-sm">
                                                    Telat
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-300 font-medium px-2">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($attendance->check_out)
                                        <span
                                            class="font-bold text-amber-700 bg-amber-50 ring-1 ring-amber-600/20 px-2.5 py-1 rounded-md text-xs tracking-wide">
                                            {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }} WIB
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-500 tracking-wide">
                                            Belum Pulang
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($attendance->latitude_in)
                                        <div class="flex flex-col gap-2 text-xs font-medium">
                                            <a href="http://maps.google.com/maps?q={{ $attendance->latitude_in }},{{ $attendance->longitude_in }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1.5 text-gray-600 hover:text-emerald-600 transition-colors w-fit">
                                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-500"></i> Maps
                                                Masuk
                                            </a>

                                            @if ($attendance->latitude_out)
                                                <a href="http://maps.google.com/maps?q={{ $attendance->latitude_out }},{{ $attendance->longitude_out }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1.5 text-gray-600 hover:text-amber-600 transition-colors w-fit">
                                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-amber-500"></i> Maps
                                                    Pulang
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-300 px-2">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-3">

                                        <div class="flex flex-col items-center gap-1.5">
                                            <div
                                                class="w-10 h-10 rounded-lg overflow-hidden ring-1 ring-gray-200 bg-gray-50 hover:ring-2 hover:ring-emerald-400 hover:shadow-md transition-all cursor-zoom-in">
                                                @if ($attendance->photo_in)
                                                    <img src="{{ asset('storage/attendance/' . $attendance->photo_in) }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <div
                                                        class="w-full h-full flex items-center justify-center text-gray-300">
                                                        <i data-lucide="image-off" class="w-4 h-4"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <span
                                                class="text-[9px] font-bold text-gray-400 uppercase tracking-widest bg-gray-100 px-1.5 py-0.5 rounded-sm">In</span>
                                        </div>

                                        <div class="w-px h-8 bg-gray-200/80 mb-4"></div>

                                        <div class="flex flex-col items-center gap-1.5">
                                            <div
                                                class="w-10 h-10 rounded-lg overflow-hidden ring-1 ring-gray-200 bg-gray-50 hover:ring-2 hover:ring-amber-400 hover:shadow-md transition-all cursor-zoom-in">
                                                @if ($attendance->photo_out)
                                                    <img src="{{ asset('storage/attendance/' . $attendance->photo_out) }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <div
                                                        class="w-full h-full flex items-center justify-center text-gray-300">
                                                        <i data-lucide="image-off" class="w-4 h-4"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <span
                                                class="text-[9px] font-bold text-gray-400 uppercase tracking-widest bg-gray-100 px-1.5 py-0.5 rounded-sm">Out</span>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-20 text-center">
                                    <div class="max-w-sm mx-auto flex flex-col items-center justify-center text-gray-400">
                                        <div class="bg-gray-50 p-4 rounded-full mb-4 ring-1 ring-gray-100">
                                            <i data-lucide="calendar-x" class="w-8 h-8 text-gray-300"></i>
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-900 tracking-tight">Belum Ada Data Presensi
                                        </h3>
                                        <p class="text-xs text-gray-400 mt-1">Data log riwayat presensi karyawan pada bulan
                                            ini terpantau masih kosong.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($attendances->hasPages())
            <div class="mt-4 flex justify-center sm:justify-end">
                <div class="bg-white p-2 rounded-xl border border-gray-100 shadow-sm">
                    {{ $attendances->links() }}
                </div>
            </div>
        @endif

    </div>
@endsection
