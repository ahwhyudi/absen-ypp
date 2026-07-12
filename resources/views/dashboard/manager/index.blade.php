<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Manajer - YPP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <header class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-100 text-indigo-700 flex items-center justify-center rounded-xl font-bold">
                <i data-lucide="briefcase" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="font-bold text-gray-900 leading-tight">Panel Manajer</h1>
                <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider">Sistem Presensi YPP</p>
            </div>
        </div>
        <div class="text-right flex items-center gap-4">
            <div class="hidden sm:block">
                <span class="block text-sm font-bold text-gray-900">{{ auth()->user()->name }}</span>
                <span class="text-xs text-indigo-600 font-semibold">Manajer Divisi</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>6
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8 space-y-10">

        @if (session('success'))
            <div class="p-4 rounded-xl border bg-emerald-50 border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-2 animate-fade-in">
                <i data-lucide="check-circle-2" class="w-5 h-5 shrink-0"></i>
                {{ session('success') }}
            </div>
        @endif

        <div>
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-amber-500"></i>
                    <h2 class="text-lg font-bold text-slate-900">Persetujuan Cuti & Izin Staf</h2>
                </div>
                @if ($log_cuti->count() > 0)
                    <span class="bg-amber-500 text-white text-[10px] uppercase tracking-wider font-bold px-3 py-1 rounded-full shadow-sm animate-pulse">
                        {{ $log_cuti->count() }} Menunggu
                    </span>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <th class="px-5 py-4">Nama Staf</th>
                                <th class="px-5 py-4">Jenis</th>
                                <th class="px-5 py-4">Rentang Tanggal</th>
                                <th class="px-5 py-4 w-1/3">Alasan</th>
                                <th class="px-5 py-4 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                            @forelse ($log_cuti as $cuti)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $cuti->user->name ?? 'Unknown' }}</td>
                                    
                                    <td class="px-5 py-4">
                                        @php
                                            $badgeColor = match($cuti->type) {
                                                'sick' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20',
                                                'leave' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20',
                                                default => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
                                            };
                                            $labelJenis = match($cuti->type) {
                                                'sick' => 'Sakit',
                                                'leave' => 'Cuti',
                                                default => 'Izin',
                                            };
                                        @endphp
                                        <span class="{{ $badgeColor }} px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider">
                                            {{ $labelJenis }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-5 py-4 text-slate-500 font-medium text-xs">
                                        {{ \Carbon\Carbon::parse($cuti->start_date)->format('d/m/Y') }} 
                                        <span class="mx-1 text-gray-300">➜</span> 
                                        {{ \Carbon\Carbon::parse($cuti->end_date)->format('d/m/Y') }}
                                    </td>
                                    
                                    <td class="px-5 py-4 text-slate-500">
                                        <p class="text-xs line-clamp-2" title="{{ $cuti->reason }}">{{ $cuti->reason }}</p>
                                    </td>
                                    
                                    <td class="px-5 py-4">
                                        <div class="flex justify-center gap-2">
                                            <form action="{{ route('manager.izin.update', $cuti->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menyetujui?')">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="tindakan" value="setuju">
                                                <button type="submit" class="bg-emerald-50 hover:bg-emerald-500 text-emerald-600 hover:text-white border border-emerald-200 text-[11px] font-bold px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                                                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Setuju
                                                </button>
                                            </form>

                                            <form action="{{ route('manager.izin.update', $cuti->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak?')">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="tindakan" value="tolak">
                                                <button type="submit" class="bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white border border-rose-200 text-[11px] font-bold px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                                                    <i data-lucide="x" class="w-3.5 h-3.5"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center">
                                            <i data-lucide="inbox" class="w-8 h-8 mb-2 text-slate-300"></i>
                                            <p class="text-sm">Tidak ada pengajuan izin yang menunggu persetujuan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            <div class="mb-4 flex items-center gap-2">
                <i data-lucide="users" class="w-5 h-5 text-indigo-500"></i>
                <h2 class="text-lg font-bold text-slate-900">Pantauan Kehadiran Anggota Tim</h2>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <th class="px-5 py-4">Nama Karyawan</th>
                                <th class="px-5 py-4">Tanggal</th>
                                <th class="px-5 py-4">Jam Masuk</th>
                                <th class="px-5 py-4">Jam Pulang</th>
                                <th class="px-5 py-4 text-center">Foto Selfie</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                            @forelse ($log_tim as $absen)
                                <tr class="hover:bg-slate-50/80 transition-all">
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $absen->user->name ?? 'Unknown' }}</td>
                                    
                                    <td class="px-5 py-4 text-slate-500">
                                        {{ \Carbon\Carbon::parse($absen->date)->format('d/m/Y') }}
                                    </td>
                                    
                                    <td class="px-5 py-4">
                                        @if ($absen->check_in)
                                            <span class="text-emerald-600 font-bold bg-emerald-50 px-2 py-1 rounded border border-emerald-100 text-xs">
                                                {{ \Carbon\Carbon::parse($absen->check_in)->format('H:i') }} WIB
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-5 py-4">
                                        @if ($absen->check_out)
                                            <span class="text-amber-600 font-bold bg-amber-50 px-2 py-1 rounded border border-amber-100 text-xs">
                                                {{ \Carbon\Carbon::parse($absen->check_out)->format('H:i') }} WIB
                                            </span>
                                        @else
                                            <span class='text-gray-400 font-medium italic text-[11px] bg-gray-50 px-2 py-1 rounded border border-gray-100'>Belum Pulang</span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-5 py-4 text-center">
                                        @if ($absen->photo_in)
                                            <a href="{{ asset('storage/' . $absen->photo_in) }}" target="_blank" class="inline-block hover:scale-110 transition-transform">
                                                <img src="{{ asset('storage/' . $absen->photo_in) }}" alt="Selfie" class="w-9 h-9 object-cover rounded-lg border border-slate-200 shadow-sm mx-auto">
                                            </a>
                                        @else
                                            <div class="w-9 h-9 bg-slate-50 rounded-lg flex items-center justify-center text-slate-300 mx-auto border border-dashed border-gray-200">
                                                <i data-lucide="image-off" class="w-4 h-4"></i>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-400 italic">
                                        <div class="flex flex-col items-center">
                                            <i data-lucide="calendar-x" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                            <p class="text-sm">Belum ada data presensi anggota tim hari ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <script>lucide.createIcons();</script>
</body>
</html>