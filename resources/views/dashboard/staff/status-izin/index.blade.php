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
                    @if ($pendingCount > 0)
                        <span
                            class="bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}
                            Pending</span>
                    @endif
                </h1>
                <p class="text-xs text-gray-400 mt-0.5">Pantau hasil persetujuan izin, cuti, dan sakit Anda</p>
            </div>
            <a href="{{ route('leave-request.index') }}"
                class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold px-4 py-2.5 rounded-xl shadow-md transition">
                <i data-lucide="plus" class="w-4 h-4"></i> Ajukan Baru
            </a>
        </div>

        @if ($leaveRequests->count())
            <div class="space-y-4">
                @foreach ($leaveRequests as $request)
                    @php
                        $statusClass = match ($request->status) {
                            'approved' => 'border-emerald-500',
                            'rejected' => 'border-red-500',
                            default => 'border-amber-500',
                        };

                        $badgeClass = match ($request->status) {
                            'approved' => 'bg-emerald-100 text-emerald-800',
                            'rejected' => 'bg-gray-200 text-gray-500',
                            default => 'bg-amber-100 text-amber-800',
                        };

                        $icon = match ($request->status) {
                            'approved' => 'check-circle-2',
                            'rejected' => 'x-circle',
                            default => 'clock',
                        };

                        $jenisBadge = match ($request->type) {
                            'permission' => 'bg-green-100 text-green-700',
                            'sick' => 'bg-rose-100 text-rose-700',
                            'leave' => 'bg-blue-100 text-blue-700',
                        };
                    @endphp
                    <div
                        class="bg-white rounded-2xl shadow-sm border-l-4 {{ $statusClass }} p-5 hover:shadow-md transition">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5">
                                    <i data-lucide="<?= $icon ?>"
                                        class="w-5 h-5 <?= $request->status === 'Disetujui' ? 'text-emerald-600' : ($request->status === 'Ditolak' ? 'text-gray-400' : 'text-amber-500') ?>"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span
                                            class="text-xs font-bold px-2 py-0.5 rounded-md uppercase {{ $jenisBadge }}">{{ ucfirst($request->type) }}
                                        </span>
                                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $badgeClass }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ $request->start_date->format('d M Y') }}
                                        @if ($request->start_date !== $request->end_date)
                                            &rarr; {{ $request->end_date->format('d M Y') }}
                                        @endif;
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $request->reason }}
                                    </p>
                                    @if (!empty($request->document_path))
                                        <a href="{{ $request->document_path }}" target="_blank"
                                            class="inline-flex items-center gap-1 text-xs text-amber-600 hover:underline mt-1 font-medium">
                                            <i data-lucide="paperclip" class="w-3 h-3"></i> Lihat Dokumen Pendukung
                                        </a>
                                    @endif;
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-xs text-gray-400">Diajukan</p>
                                <p class="text-xs font-semibold text-gray-600">
                                    {{ $request->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        @if ($request->status === 'pending')
                            <div
                                class="mt-3 pt-3 border-t border-amber-100 flex items-center gap-2 text-xs text-amber-700 font-medium">
                                <i data-lucide="info" class="w-3.5 h-3.5"></i>
                                Pengajuan Anda sedang menunggu tinjauan dari Admin HRD. Harap bersabar.
                            </div>
                        @elseif ($request->status === 'rejected')
                            <div
                                class="mt-3 pt-3 border-t border-gray-200 flex items-center gap-2 text-xs text-gray-500 font-medium">
                                <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                                Pengajuan ini ditolak. Hubungi Admin HRD untuk informasi lebih lanjut.
                            </div>
                        @endif;
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 text-gray-300"></i>
                <p class="text-gray-500 font-medium">Belum ada pengajuan izin.</p>
                <a href="tambah_cuti.php"
                    class="mt-4 inline-flex items-center gap-2 text-amber-600 text-sm font-bold hover:underline">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Buat Pengajuan Pertama
                </a>
            </div>
        @endif;

        <!-- Bottom nav -->

    </main>
    @include('includes.components.navbar')

    <script>
        lucide.createIcons();
    </script>
</body>

</html>
