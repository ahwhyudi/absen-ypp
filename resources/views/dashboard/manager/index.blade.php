<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Manajer - YPP</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <?php tampilkanHeader($nama_manajer, "Manajer Divisi", "manajer"); ?>

    <main class="max-w-6xl mx-auto px-4 py-8 sm:px-6 lg:px-8 space-y-10">

        <!-- Persetujuan Cuti/Izin -->
        <div>
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-amber-500"></i>
                    <h2 class="text-lg font-bold text-slate-900">Persetujuan Cuti & Izin Staf</h2>
                </div>
                <?php
                $cnt = $log_cuti ? mysqli_num_rows($log_cuti) : 0;
                if ($cnt > 0):
                ?>
                <span class="bg-amber-500 text-white text-xs font-bold px-2.5 py-1 rounded-full animate-pulse"><?php echo $cnt; ?> Menunggu</span>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <th class="px-5 py-4">Nama Staf</th>
                                <th class="px-5 py-4">Jenis</th>
                                <th class="px-5 py-4">Rentang Tanggal</th>
                                <th class="px-5 py-4">Alasan</th>
                                <th class="px-5 py-4 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                            <?php if ($log_cuti && mysqli_num_rows($log_cuti) > 0):
                                while ($cuti = mysqli_fetch_assoc($log_cuti)): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-4 font-semibold text-slate-900"><?php echo htmlspecialchars($cuti['nama_lengkap']); ?></td>
                                    <td class="px-5 py-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase 
                                            <?php echo $cuti['jenis_izin'] == 'Sakit' ? 'bg-rose-100 text-rose-700' : ($cuti['jenis_izin'] == 'Cuti' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700'); ?>">
                                            <?php echo htmlspecialchars($cuti['jenis_izin']); ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600 font-medium">
                                        <?php echo date('d/m/Y', strtotime($cuti['tanggal_mulai'])); ?> s/d <?php echo date('d/m/Y', strtotime($cuti['tanggal_selesai'])); ?>
                                    </td>
                                    <td class="px-5 py-4 text-slate-500 max-w-xs break-words"><?php echo htmlspecialchars($cuti['keterangan']); ?></td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-center gap-2">
                                            <a href="pantauan.php?aksi=setuju&id_cuti=<?php echo $cuti['id']; ?>" 
                                               class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold px-3 py-1.5 rounded-xl shadow-sm transition-all flex items-center gap-1">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i> Setujui
                                            </a>
                                            <a href="pantauan.php?aksi=tolak&id_cuti=<?php echo $cuti['id']; ?>"
                                               class="bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold px-3 py-1.5 rounded-xl shadow-sm transition-all flex items-center gap-1">
                                                <i data-lucide="x" class="w-3.5 h-3.5"></i> Tolak
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-slate-400 italic">
                                        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                        Tidak ada pengajuan izin/cuti yang menunggu persetujuan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pantauan Kehadiran Tim -->
        <div>
            <div class="mb-4 flex items-center gap-2">
                <i data-lucide="users" class="w-5 h-5 text-amber-500"></i>
                <h2 class="text-lg font-bold text-slate-900">Pantauan Kehadiran Anggota Tim</h2>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <th class="px-5 py-4">Nama Karyawan</th>
                                <th class="px-5 py-4">Tanggal</th>
                                <th class="px-5 py-4">Jam Masuk</th>
                                <th class="px-5 py-4">Jam Pulang</th>
                                <th class="px-5 py-4">Koordinat GPS</th>
                                <th class="px-5 py-4 text-center">Foto Selfie</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                            <?php if ($log_tim && mysqli_num_rows($log_tim) > 0):
                                while ($row = mysqli_fetch_assoc($log_tim)): ?>
                                <tr class="hover:bg-slate-50/80 transition-all">
                                    <td class="px-5 py-4 font-semibold text-slate-900"><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                    <td class="px-5 py-4 text-slate-500"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                    <td class="px-5 py-4 text-emerald-600 font-bold"><?php echo $row['jam_masuk'] ? $row['jam_masuk'] . ' WIB' : '-'; ?></td>
                                    <td class="px-5 py-4 text-amber-600 font-bold">
                                        <?php echo !empty($row['jam_pulang']) ? $row['jam_pulang'] . ' WIB' : "<span class='text-gray-400 font-normal italic text-xs'>Belum Pulang</span>"; ?>
                                    </td>
                                    <td class="px-5 py-4 font-mono text-xs text-slate-400"><?php echo $row['lintang_masuk']; ?>, <?php echo $row['bujur_masuk']; ?></td>
                                    <td class="px-5 py-4 text-center">
                                        <?php if (!empty($row['jalur_foto_masuk']) && file_exists("../" . $row['jalur_foto_masuk'])): ?>
                                            <img src="../<?php echo $row['jalur_foto_masuk']; ?>" alt="Selfie" 
                                                 class="w-10 h-10 object-cover rounded-lg border border-slate-200 shadow-sm mx-auto img-preview-hover">
                                        <?php else: ?>
                                            <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-300 text-[10px] mx-auto border border-dashed">N/A</div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-slate-400 italic">
                                        <i data-lucide="calendar-x" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                        Belum ada data presensi anggota tim.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center pb-4">
            <a href="../logout.php" class="text-xs text-rose-400 hover:text-rose-600 font-medium transition-colors">
                Keluar dari Sistem
            </a>
        </div>
    </main>

    <link rel="stylesheet" href="../aset/css/style.css">
    <script>lucide.createIcons();</script>
</body>
</html>