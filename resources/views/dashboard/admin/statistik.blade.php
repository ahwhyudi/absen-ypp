@extends('dashboard.admin.index')

@section('content')
    <div class="space-y-6 pt-16 lg:pt-0 max-w-[1600px] mx-auto antialiased">

        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div class="space-y-1">
                <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Statistik Kehadiran Karyawan</h2>
                <p class="text-sm text-gray-500">Ringkasan ketepatan waktu dan kehadiran bulanan seluruh staff.</p>
            </div>

            <form method="GET" action="{{ route('admin.statistik.index') }}"
                class="flex items-center gap-3 bg-white px-4 py-2.5 rounded-xl border border-gray-100 shadow-sm w-fit">
                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Filter Bulan:</label>
                <div class="relative">
                    <input type="month" name="bulan" value="{{ $filter_bulan }}" onchange="this.form.submit()"
                        class="bg-gray-50 border border-gray-200 text-gray-800 font-semibold text-sm rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all cursor-pointer">
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="w-5 h-5 text-amber-500"></i>
                Grafik Kehadiran vs Keterlambatan
            </h3>
            <div class="relative h-[300px] w-full">
                <canvas id="chartKehadiran"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead
                        class="bg-gray-50/80 text-[11px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">Nama Karyawan</th>
                            <th class="px-6 py-4 text-center">Total Hadir</th>
                            <th class="px-6 py-4 text-center">Tepat Waktu</th>
                            <th class="px-6 py-4 text-center">Terlambat</th>
                            <th class="px-6 py-4 text-center">Izin Disetujui</th>
                            <th class="px-6 py-4 text-center">Tidak Absen Pulang</th>
                            <th class="px-6 py-4 text-center">Skor Disiplin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @forelse ($statistics as $index => $row)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4 text-gray-400 font-medium">{{ $index + 1 }}</td>

                                <td class="px-6 py-4">
                                    <span class="font-bold text-gray-900 group-hover:text-amber-600 transition-colors">
                                        {{ $row['nama'] }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center font-bold text-gray-800">
                                    {{ $row['total_hadir'] }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold text-emerald-600">{{ $row['tepat_waktu'] }}</span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($row['terlambat'] > 0)
                                        <span
                                            class="bg-rose-50 text-rose-700 ring-1 ring-rose-600/20 px-2 py-0.5 rounded-md text-[11px] font-bold shadow-sm">
                                            {{ $row['terlambat'] }}x
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($row['total_izin'] > 0)
                                        <span class="text-blue-600 font-bold">{{ $row['total_izin'] }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($row['tidak_pulang'] > 0)
                                        <span
                                            class="bg-amber-50 text-amber-700 ring-1 ring-amber-600/20 px-2 py-0.5 rounded-md text-[11px] font-bold shadow-sm">
                                            {{ $row['tidak_pulang'] }}x
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="font-black text-lg {{ $row['skor_color'] }}">
                                        {{ $row['skor'] }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <div class="bg-gray-50 p-4 rounded-full mb-3 ring-1 ring-gray-100">
                                            <i data-lucide="bar-chart-3" class="w-8 h-8 text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900">Belum Ada Data</p>
                                        <p class="text-xs mt-1 text-gray-400">Tidak ada data statistik untuk bulan ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labels = @json($labels);
    const dataHadir = @json($dataHadir);
    const dataTerlambat = @json($dataTerlambat);

    const canvas = document.getElementById('chartKehadiran');
    const ctx = canvas.getContext('2d');
    
    // ==========================================
    // JURUS RAHASIA: MEMBUAT GRADIENT WARNA PREMIUM
    // ==========================================
    // Gradasi untuk "Total Hadir" (Emerald/Hijau SaaS)
    const gradientHadir = ctx.createLinearGradient(0, 0, 0, 300);
    gradientHadir.addColorStop(0, 'rgba(16, 185, 129, 0.85)');   // Emerald 500 Pekat di atas
    gradientHadir.addColorStop(0.5, 'rgba(16, 185, 129, 0.3)'); // Memudar di tengah
    gradientHadir.addColorStop(1, 'rgba(16, 185, 129, 0.02)');  // Hampir transparan di bawah

    // Gradasi untuk "Terlambat" (Rose/Merah Glamour)
    const gradientTerlambat = ctx.createLinearGradient(0, 0, 0, 300);
    gradientTerlambat.addColorStop(0, 'rgba(244, 63, 94, 0.85)');   // Rose 500 Pekat di atas
    gradientTerlambat.addColorStop(0.5, 'rgba(244, 63, 94, 0.3)'); // Memudar di tengah
    gradientTerlambat.addColorStop(1, 'rgba(244, 63, 94, 0.02)');  // Hampir transparan di bawah

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total Hadir',
                    data: dataHadir,
                    backgroundColor: gradientHadir,
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 2,
                    borderRadius: { topLeft: 8, topRight: 8, bottomLeft: 0, bottomRight: 0 }, // Hanya melengkung di atas
                    borderSkipped: false,
                    maxBarThickness: 28, // Biar batang stabil gak kegemukan kalau staff dikit
                    hoverBackgroundColor: 'rgba(16, 185, 129, 0.95)', // Efek pop-out pas di-hover
                    hoverBorderColor: 'rgb(5, 150, 105)',
                },
                {
                    label: 'Terlambat',
                    data: dataTerlambat,
                    backgroundColor: gradientTerlambat,
                    borderColor: 'rgb(244, 63, 94)',
                    borderWidth: 2,
                    borderRadius: { topLeft: 8, topRight: 8, bottomLeft: 0, bottomRight: 0 },
                    borderSkipped: false,
                    maxBarThickness: 28,
                    hoverBackgroundColor: 'rgba(244, 63, 94, 0.95)',
                    hoverBorderColor: 'rgb(225, 29, 72)',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index', // Pas kursor lewat, langsung nampilin data hadir & telat sekaligus
            },
            plugins: {
                legend: { 
                    position: 'top',
                    align: 'end', // Taruh legend di kanan atas biar clean
                    labels: {
                        usePointStyle: true, // Ubah kotak legend jadi buatan lingkaran estetik
                        pointStyle: 'circle',
                        padding: 25,
                        boxWidth: 8,
                        boxHeight: 8,
                        font: { family: "'Plus Jakarta Sans', 'Inter', sans-serif", size: 12, weight: '600' },
                        color: '#475569'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)', // Slate 950 Glassmorphism
                    padding: 14,
                    titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 13, weight: '700' },
                    bodyFont: { family: "'Inter', sans-serif", size: 12 },
                    cornerRadius: 12,
                    boxPadding: 6,
                    usePointStyle: true,
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed.y !== null) { label += context.parsed.y + ' Hari'; }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    border: { display: false }, // Hapus garis vertikal paling kiri
                    ticks: { 
                        stepSize: 1, 
                        color: '#94a3b8',
                        font: { family: "'Inter', sans-serif", size: 11, weight: '500' } 
                    },
                    grid: { 
                        color: '#f1f5f9',
                        tickBorderDash: [4, 4],
                        drawTicks: false
                    }
                },
                x: {
                    border: { display: false },
                    ticks: { 
                        color: '#64748b',
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '600' } 
                    },
                    grid: { display: false } // Hapus grid background biar gak pusing liatnya
                }
            },
            animation: {
                duration: 1200,
                easing: 'easeOutQuart' // Animasi naik melambat yang smooth pas halaman di-load
            }
        }
    });
</script>
@endsection
