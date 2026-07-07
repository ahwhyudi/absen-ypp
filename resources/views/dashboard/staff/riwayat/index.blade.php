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
                    <input type="month" name="bulan" value="<?= htmlspecialchars($filter_bulan) ?>"
                           onchange="this.form.submit()"
                           class="bg-white border border-gray-200 text-gray-800 text-sm rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </form>
            </div>
        </div>

        <!-- Kartu Statistik -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm text-center">
                <p class="text-2xl font-extrabold text-gray-900"><?= $stat['total_hadir'] ?></p>
                <p class="text-xs text-gray-400 mt-1 font-medium">Hari Hadir</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-emerald-100 shadow-sm text-center">
                <p class="text-2xl font-extrabold text-emerald-600"><?= $stat['tepat_waktu'] ?></p>
                <p class="text-xs text-gray-400 mt-1 font-medium">Tepat Waktu</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-rose-100 shadow-sm text-center">
                <p class="text-2xl font-extrabold text-rose-500"><?= $stat['terlambat'] ?></p>
                <p class="text-xs text-gray-400 mt-1 font-medium">Terlambat</p>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-blue-100 shadow-sm text-center">
                <p class="text-2xl font-extrabold text-blue-500"><?= $stat['sudah_pulang'] ?></p>
                <p class="text-xs text-gray-400 mt-1 font-medium">Lengkap (Masuk+Pulang)</p>
            </div>
        </div>

        <!-- Tabel Riwayat -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-wider">
                            <th class="px-5 py-4">Tanggal</th>
                            <th class="px-5 py-4">Jam Masuk</th>
                            <th class="px-5 py-4">Jam Pulang</th>
                            <th class="px-5 py-4 text-center">Status</th>
                            <th class="px-5 py-4">Lokasi Masuk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        <?php if ($hasil_absen && $hasil_absen->num_rows > 0):
                            while ($row = $hasil_absen->fetch_assoc()):
                                $terlambat = strtotime($row['jam_masuk']) > strtotime('08:00:00');
                                $belum_pulang = empty($row['jam_pulang']);
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4 font-semibold text-gray-900">
                                <?= date('D, d M Y', strtotime($row['tanggal'])) ?>
                            </td>
                            <td class="px-5 py-4 font-mono <?= $terlambat ? 'text-rose-600 font-bold' : 'text-emerald-700 font-bold' ?>">
                                <?= $row['jam_masuk'] ?> WIB
                                <?php if ($terlambat): ?>
                                    <span class="ml-1 text-[10px] bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded-full font-bold">TERLAMBAT</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 font-mono text-gray-600">
                                <?= !$belum_pulang ? $row['jam_pulang'] . ' WIB' : '<span class="text-amber-500 font-medium italic text-xs">Belum absen pulang</span>' ?>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <?php if ($belum_pulang): ?>
                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full uppercase">Setengah</span>
                                <?php elseif ($terlambat): ?>
                                    <span class="px-2 py-1 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-full uppercase">Terlambat</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full uppercase">Tepat Waktu</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-400 font-mono">
                                <?php if ($row['lintang_masuk']): ?>
                                    <a href="https://www.google.com/maps?q=<?= $row['lintang_masuk'] ?>,<?= $row['bujur_masuk'] ?>" 
                                       target="_blank" class="text-amber-600 hover:underline flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                                        <?= number_format($row['lintang_masuk'], 4) ?>, <?= number_format($row['bujur_masuk'], 4) ?>
                                    </a>
                                <?php else: ?>—<?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile;
                        else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                <i data-lucide="calendar-x" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                                Tidak ada data absensi untuk bulan ini.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bottom nav mobile -->
       
    </main>
    @include('includes.components.navbar')

    <script>lucide.createIcons();</script>
</body>
</html>
