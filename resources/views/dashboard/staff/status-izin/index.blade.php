<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Izin / Cuti Saya - YPP</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased">

@include('includes.components.header')
    <main class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl font-extrabold text-gray-900 flex items-center gap-2">
                    Status Pengajuan Izin Saya
                    <?php if ($c_pending > 0): ?>
                        <span class="bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"><?= $c_pending ?> Pending</span>
                    <?php endif; ?>
                </h1>
                <p class="text-xs text-gray-400 mt-0.5">Pantau hasil persetujuan izin, cuti, dan sakit Anda</p>
            </div>
            <a href="tambah_cuti.php" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold px-4 py-2.5 rounded-xl shadow-md transition">
                <i data-lucide="plus" class="w-4 h-4"></i> Ajukan Baru
            </a>
        </div>

        <?php if ($hasil && $hasil->num_rows > 0): ?>
            <div class="space-y-4">
            <?php while ($row = $hasil->fetch_assoc()): 
                $status_class = match($row['status']) {
                    'Disetujui' => 'bg-emerald-50 border-emerald-200',
                    'Ditolak'   => 'bg-gray-50 border-gray-200',
                    default     => 'bg-amber-50 border-amber-200',
                };
                $badge_class = match($row['status']) {
                    'Disetujui' => 'bg-emerald-100 text-emerald-800',
                    'Ditolak'   => 'bg-gray-200 text-gray-500',
                    default     => 'bg-amber-100 text-amber-800',
                };
                $icon = match($row['status']) {
                    'Disetujui' => 'check-circle-2',
                    'Ditolak'   => 'x-circle',
                    default     => 'clock',
                };
                $jenis_badge = match($row['jenis_izin']) {
                    'Sakit' => 'bg-rose-100 text-rose-700',
                    'Cuti'  => 'bg-blue-100 text-blue-700',
                    default => 'bg-amber-100 text-amber-700',
                };
            ?>
                <div class="bg-white rounded-2xl border <?= $status_class ?> shadow-sm p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5">
                                <i data-lucide="<?= $icon ?>" class="w-5 h-5 <?= $row['status']==='Disetujui' ? 'text-emerald-600' : ($row['status']==='Ditolak' ? 'text-gray-400' : 'text-amber-500') ?>"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-md uppercase <?= $jenis_badge ?>"><?= htmlspecialchars($row['jenis_izin']) ?></span>
                                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full <?= $badge_class ?>"><?= htmlspecialchars($row['status']) ?></span>
                                </div>
                                <p class="text-sm font-semibold text-gray-800">
                                    <?= date('d M Y', strtotime($row['tanggal_mulai'])) ?>
                                    <?php if ($row['tanggal_mulai'] !== $row['tanggal_selesai']): ?>
                                        &rarr; <?= date('d M Y', strtotime($row['tanggal_selesai'])) ?>
                                    <?php endif; ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($row['keterangan']) ?></p>
                                <?php if (!empty($row['jalur_dokumen'])): ?>
                                    <a href="<?= htmlspecialchars($row['jalur_dokumen']) ?>" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs text-amber-600 hover:underline mt-1 font-medium">
                                        <i data-lucide="paperclip" class="w-3 h-3"></i> Lihat Dokumen Pendukung
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs text-gray-400">Diajukan</p>
                            <p class="text-xs font-semibold text-gray-600"><?= date('d M Y, H:i', strtotime($row['tanggal_diajukan'])) ?></p>
                        </div>
                    </div>

                    <?php if ($row['status'] === 'Pending'): ?>
                    <div class="mt-3 pt-3 border-t border-amber-100 flex items-center gap-2 text-xs text-amber-700 font-medium">
                        <i data-lucide="info" class="w-3.5 h-3.5"></i>
                        Pengajuan Anda sedang menunggu tinjauan dari Admin HRD. Harap bersabar.
                    </div>
                    <?php elseif ($row['status'] === 'Ditolak'): ?>
                    <div class="mt-3 pt-3 border-t border-gray-200 flex items-center gap-2 text-xs text-gray-500 font-medium">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                        Pengajuan ini ditolak. Hubungi Admin HRD untuk informasi lebih lanjut.
                    </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 text-gray-300"></i>
                <p class="text-gray-500 font-medium">Belum ada pengajuan izin.</p>
                <a href="tambah_cuti.php" class="mt-4 inline-flex items-center gap-2 text-amber-600 text-sm font-bold hover:underline">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Buat Pengajuan Pertama
                </a>
            </div>
        <?php endif; ?>

        <!-- Bottom nav -->
       
    </main>
    @include('includes.components.navbar')

    <script>lucide.createIcons();</script>
</body>
</html>
