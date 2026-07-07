<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - YPP</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="../aset/css/style.css">
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased flex min-h-screen overflow-x-hidden">

    <!-- Sidebar -->
    <?php $active_menu = 'dasbor'; include '../komponen/sidebar_admin.php'; ?>

    <div class="flex-1 flex flex-col min-w-0 pt-14 lg:pt-0">
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex justify-between items-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sistem Presensi Geotagging — Yayasan Prasasti Perdamaian</span>
            <div class="text-right">
                <span class="block text-sm font-bold text-gray-900"><?php echo htmlspecialchars($nama_admin); ?></span>
                <span class="text-xs text-emerald-600 font-semibold flex items-center justify-end gap-1">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Hak Akses: Admin HRD
                </span>
            </div>
        </header>

        <main class="flex-1 p-4 lg:p-8 pt-16 lg:pt-8 min-w-0 overflow-x-auto space-y-6">
           <!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <!-- 1. Total Presensi -->
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-xl justify-center shrink-0">
            <i data-lucide="clock-check" class="w-6 h-6 text-emerald-600"></i>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-gray-900"><?php echo isset($q_hadir['jml']) ? $q_hadir['jml'] : 0; ?></p>
            <p class="text-xs text-gray-400 font-medium">Total Presensi (Filter Aktif)</p>
        </div>
    </div>

    <!-- 2. Izin/Cuti Menunggu Persetujuan -->
<div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
        <i data-lucide="mail" class="w-6 h-6 text-amber-600"></i>
    </div>
    <div>
        <p class="text-2xl font-extrabold text-gray-900">
            <?php echo isset($c_pending['jml']) ? $c_pending['jml'] : 0; ?>
        </p>
        <p class="text-xs text-gray-400 font-medium">Izin/Cuti Menunggu Persetujuan</p>
        <!-- Tambahan teks indikator biar lebih informatif -->
        <?php if (isset($c_pending['jml']) && $c_pending['jml'] > 0): ?>
            <span class="text-[10px] text-amber-600 font-bold bg-amber-50 px-1.5 py-0.5 rounded-full mt-1 inline-block animate-pulse">Perlu Diproses</span>
        <?php else: ?>
            <span class="text-[10px] text-emerald-600 font-medium bg-emerald-50 px-1.5 py-0.5 rounded-full mt-1 inline-block">Semua Beres ✓</span>
        <?php endif; ?>
    </div>
</div>

    <!-- 3. Total Staff Terdaftar -->
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
            <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-gray-900"><?php echo isset($c_staff['jml']) ? $c_staff['jml'] : 0; ?></p>
            <p class="text-xs text-gray-400 font-medium">Total Staff Terdaftar</p>
        </div>
    </div>
</div>
            <div>
                <h2 class="text-xl font-extrabold text-gray-900 tracking-tight mb-1">Log Riwayat Presensi Masuk & Pulang</h2>
                <p class="text-xs text-gray-400">Seluruh data koordinat geotagging, nama wilayah, dan foto yang terekam.</p>
            </div>

            <!-- Filter -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap items-end justify-between gap-4">
                <form method="GET" action="dasbor.php" class="flex flex-wrap items-center gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Rekap Bulanan</label>
                        <input type="month" name="filter_bulan" value="<?php echo htmlspecialchars($filter_bulan); ?>" 
                               class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-amber-500 text-gray-700">
                    </div>
                    <div class="text-xs font-bold text-gray-300 uppercase py-2">atau</div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Rekap Mingguan</label>
                        <input type="week" name="filter_minggu" value="<?php echo htmlspecialchars($filter_minggu); ?>"
                               class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-amber-500 text-gray-700">
                    </div>
                    <div class="flex gap-2 pt-5">
                        <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-xl text-sm transition-all flex items-center gap-1.5 shadow-sm">
                            <i data-lucide="search" class="w-4 h-4 text-amber-400"></i> Filter Data
                        </button>
                        <a href="dasbor.php" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-4 py-2 rounded-xl text-sm transition-all flex items-center gap-1.5">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i> Reset
                        </a>
                    </div>
                </form>
                <a href="dasbor.php?aksi=unduh_excel&filter_bulan=<?php echo htmlspecialchars($filter_bulan); ?>&filter_minggu=<?php echo htmlspecialchars($filter_minggu); ?>" 
                   class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm transition-all flex items-center gap-2 shadow-sm">
                    <i data-lucide="download" class="w-4 h-4"></i> Download Excel
                </a>
            </div>

            <!-- Tabel Data -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100">
                                <th class="px-6 py-4">Nama Staff</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Jam Masuk</th>
                                <th class="px-6 py-4">Jam Pulang</th>
                                <th class="px-6 py-4">Lokasi Wilayah & GPS</th>
                                <th class="px-6 py-4 text-center">Foto Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php if ($eksekusi && mysqli_num_rows($eksekusi) > 0):
                                $index = 0;
                                while ($row = mysqli_fetch_assoc($eksekusi)):
                                    $index++;
                            ?>
                                <tr class="hover:bg-gray-50/50 transition-all">
                                    <td class="px-6 py-4 font-bold text-gray-900"><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                    <td class="px-6 py-4 font-medium text-gray-500"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                    <td class="px-6 py-4 font-bold">
                                        <?php 
                                        $jam_m = $row['jam_masuk'];
                                        $terlambat_flag = $jam_m && $jam_m > '08:00:00';
                                        echo $jam_m ? "<span class='" . ($terlambat_flag ? "text-rose-600" : "text-emerald-600") . "'>" . $jam_m . " WIB</span>" : "-";
                                        if ($terlambat_flag) echo " <span class='text-[10px] bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded-full font-bold ml-1'>TERLAMBAT</span>";
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 text-amber-600 font-bold">
                                        <?php echo !empty($row['jam_pulang']) ? $row['jam_pulang'] . " WIB" : "<span class='text-gray-400 font-normal italic text-xs'>Belum Pulang</span>"; ?>
                                    </td>
                                    <td class="px-6 py-4 space-y-2.5">
                                        <div class="text-xs">
                                            <span class="bg-emerald-50 text-emerald-700 font-bold px-1.5 py-0.5 rounded border border-emerald-100">📌 MASUK:</span>
                                            <span class="text-gray-700 font-medium ml-1" id="wilayah_masuk_<?php echo $index; ?>">Memuat...</span>
                                            <span class="block font-mono text-[10px] text-gray-400 mt-0.5"><?php echo $row['lintang_masuk'] . ", " . $row['bujur_masuk']; ?></span>
                                        </div>
                                        <?php if (!empty($row['jam_pulang'])): ?>
                                        <div class="text-xs border-t border-gray-100 pt-2">
                                            <span class="bg-amber-50 text-amber-700 font-bold px-1.5 py-0.5 rounded border border-amber-100">📌 PULANG:</span>
                                            <span class="text-gray-700 font-medium ml-1" id="wilayah_pulang_<?php echo $index; ?>">Memuat...</span>
                                            <span class="block font-mono text-[10px] text-gray-400 mt-0.5"><?php echo $row['lintang_pulang'] . ", " . $row['bujur_pulang']; ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-3">
                                            <div class="text-center">
                                                <?php if (!empty($row['jalur_foto_masuk']) && file_exists("../" . $row['jalur_foto_masuk'])): ?>
                                                    <img src="../<?php echo $row['jalur_foto_masuk']; ?>" alt="Foto Masuk" class="w-10 h-10 object-cover rounded-xl border border-gray-200 shadow-sm mx-auto img-preview-hover cursor-zoom-in">
                                                <?php else: ?>
                                                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 text-[10px] border border-gray-200 mx-auto">N/A</div>
                                                <?php endif; ?>
                                                <span class="text-[10px] text-emerald-600 font-bold block mt-1">Masuk</span>
                                            </div>
                                            <div class="text-center">
                                                <?php if (!empty($row['jalur_foto_pulang']) && file_exists("../" . $row['jalur_foto_pulang'])): ?>
                                                    <img src="../<?php echo $row['jalur_foto_pulang']; ?>" alt="Foto Pulang" class="w-10 h-10 object-cover rounded-xl border border-gray-200 shadow-sm mx-auto img-preview-hover cursor-zoom-in">
                                                <?php else: ?>
                                                    <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 text-[10px] border border-dashed border-gray-200 mx-auto">Belum</div>
                                                <?php endif; ?>
                                                <span class="text-[10px] text-amber-600 font-bold block mt-1">Pulang</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <script>
                                    (function() {
                                        var lat_m = <?php echo json_encode($row['lintang_masuk']); ?>;
                                        var lon_m = <?php echo json_encode($row['bujur_masuk']); ?>;
                                        var idx   = <?php echo $index; ?>;
                                        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat_m + '&lon=' + lon_m)
                                            .then(r => r.json()).then(d => {
                                                var a = d.address || {};
                                                var parts = [a.subdistrict||a.village||a.suburb||'', a.city||a.city_district||a.county||'', a.state||''].filter(Boolean);
                                                document.getElementById('wilayah_masuk_' + idx).innerText = parts.join(', ') || 'Luar Jangkauan';
                                            }).catch(() => { document.getElementById('wilayah_masuk_' + idx).innerText = 'Gagal memuat'; });
                                        <?php if (!empty($row['jam_pulang'])): ?>
                                        var lat_p = <?php echo json_encode($row['lintang_pulang']); ?>;
                                        var lon_p = <?php echo json_encode($row['bujur_pulang']); ?>;
                                        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat_p + '&lon=' + lon_p)
                                            .then(r => r.json()).then(d => {
                                                var a = d.address || {};
                                                var parts = [a.subdistrict||a.village||a.suburb||'', a.city||a.city_district||a.county||'', a.state||''].filter(Boolean);
                                                document.getElementById('wilayah_pulang_' + idx).innerText = parts.join(', ') || 'Luar Jangkauan';
                                            }).catch(() => { document.getElementById('wilayah_pulang_' + idx).innerText = 'Gagal memuat'; });
                                        <?php endif; ?>
                                    })();
                                </script>
                            <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-14 text-center text-gray-400 italic">
                                        <i data-lucide="calendar-x" class="w-10 h-10 mx-auto mb-2 text-gray-300"></i>
                                        Tidak ada data presensi untuk periode ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($total_halaman > 1): ?>
            <div class="mt-4 flex items-center justify-between">
                <p class="text-xs text-gray-500">Menampilkan <?= min($offset + $per_page, $total_rows) ?> dari <?= $total_rows ?> data</p>
                <div class="flex gap-1">
                    <?php for ($p = 1; $p <= $total_halaman; $p++): 
                        $params = $_GET;
                        $params['hal'] = $p;
                        $url = 'dasbor.php?' . http_build_query($params);
                    ?>
                    <a href="<?= htmlspecialchars($url) ?>"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold border transition-all
                              <?= $p === $halaman ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' ?>">
                        <?= $p ?>
                    </a>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
