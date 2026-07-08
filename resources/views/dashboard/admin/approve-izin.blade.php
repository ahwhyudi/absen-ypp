@extends('dashboard.admin.index')

@section('content')
<div class="space-y-6 pt-16 lg:pt-0 max-w-[1600px] mx-auto antialiased relative">
    
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div class="space-y-1">
            <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Persetujuan Ketidakhadiran</h2>
            <p class="text-sm text-gray-500">Validasi surat izin, keterangan sakit, dan cuti tahunan milik staff.</p>
        </div>
        
        <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-xl border border-gray-100 shadow-sm">
            <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
            <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Menunggu Proses:</span>
            <span class="text-sm font-black text-gray-900">{{ $pendingCount }}</span>
        </div>
    </div>

    @if (session('success'))
        @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col z-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead class="bg-gray-50/80 text-[11px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Informasi Staff</th>
                        <th class="px-6 py-4">Jenis & Tanggal</th>
                        <th class="px-6 py-4 w-1/4">Alasan</th>
                        <th class="px-6 py-4 text-center">Dokumen</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody id="izin-table-body" class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse ($leaveRequests as $izin)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-50 to-indigo-50 flex items-center justify-center text-blue-700 font-extrabold text-xs shrink-0 ring-2 ring-white shadow-sm">
                                        {{ strtoupper(substr($izin->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {{ $izin->user->name ?? 'User Dihapus' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                @php
                                    $labelJenis = match($izin->type) {
                                        'sick' => 'Sakit',
                                        'leave' => 'Cuti',
                                        default => 'Izin',
                                    };
                                    
                                    $badgeColor = match($izin->type) {
                                        'sick' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
                                        'leave'  => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                        default => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                    };
                                @endphp
                                <span class="{{ $badgeColor }} px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider ring-1 inset-ring inline-block mb-1.5">
                                    {{ $labelJenis }}
                                </span>
                                <div class="text-xs font-medium text-gray-500">
                                    {{ \Carbon\Carbon::parse($izin->start_date)->format('d/m/Y') }} 
                                    <span class="text-gray-300 mx-1">➜</span> 
                                    {{ \Carbon\Carbon::parse($izin->end_date)->format('d/m/Y') }}
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <p class="text-xs text-gray-600 line-clamp-2" title="{{ $izin->reason }}">
                                    {{ $izin->reason }}
                                </p>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if (!empty($izin->document_path))
                                    <a href="{{ asset('storage/' . $izin->document_path) }}" target="_blank" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 hover:bg-indigo-50 text-gray-400 hover:text-indigo-600 transition-colors ring-1 ring-gray-200 hover:ring-indigo-300 cursor-pointer" title="Lihat Lampiran">
                                        <i data-lucide="paperclip" class="w-4 h-4"></i>
                                    </a>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                @php
                                    $labelStatus = match($izin->status) {
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        default    => 'Pending',
                                    };

                                    $statusColor = match($izin->status) {
                                        'approved' => 'bg-emerald-100 text-emerald-800',
                                        'rejected' => 'bg-gray-100 text-gray-500',
                                        default    => 'bg-amber-100 text-amber-800',
                                    };
                                @endphp
                                <span class="{{ $statusColor }} px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                    {{ $labelStatus }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                @if ($izin->status === 'pending')
                                    <div class="flex items-center justify-center gap-2">
                                        <form id="form-action-{{ $izin->id }}" action="{{ route('admin.izin.update', $izin->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="tindakan" id="tindakan-{{ $izin->id }}" value="">
                                            
                                            <button type="button" onclick="openConfirmModal('setuju', {{ $izin->id }}, '{{ $izin->user->name ?? 'Staff' }}')" 
                                                    class="flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 hover:border-emerald-600 text-[11px] font-bold px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i> Setuju
                                            </button>
                                        </form>

                                        <button type="button" onclick="openConfirmModal('tolak', {{ $izin->id }}, '{{ $izin->user->name ?? 'Staff' }}')" 
                                                class="flex items-center gap-1.5 bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 hover:border-rose-600 text-[11px] font-bold px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i> Tolak
                                        </button>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-50 px-2 py-1 rounded-md border border-gray-100">Diproses</span>
                                    </div>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <div class="bg-gray-50 p-4 rounded-full mb-3 ring-1 ring-gray-100">
                                        <i data-lucide="inbox" class="w-8 h-8 text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">Kotak Masuk Kosong</p>
                                    <p class="text-xs mt-1 text-gray-400">Belum ada pengajuan izin dari staff yayasan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL 1: POPUP SUCCESS (Fade-In-Down & Blur Animation)    --}}
{{-- ========================================================= --}}
@if (session('success'))
<div id="success-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300 ease-out opacity-0 translate-y-4">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeSuccessModal()"></div>
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-gray-100 z-10 text-center space-y-4 transition-all duration-300 ease-out transform scale-95">
        <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto ring-8 ring-emerald-50/50">
            <i data-lucide="check" class="w-8 h-8 text-emerald-600"></i>
        </div>
        <div class="space-y-1">
            <h4 class="text-lg font-bold text-gray-900">Berhasil!</h4>
            <p class="text-sm text-gray-500">{{ session('success') }}</p>
        </div>
        <button type="button" onclick="closeSuccessModal()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer shadow-sm">
            Selesai
        </button>
    </div>
</div>
@endif

{{-- ========================================================= --}}
{{-- MODAL 2: POPUP KONFIRMASI (Dinamis: Setuju / Tolak)       --}}
{{-- ========================================================= --}}
<div id="confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300 ease-in-out opacity-0 translate-y-4">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeConfirmModal()"></div>
    
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-gray-100 z-10 text-center space-y-4 transition-all duration-300 ease-in-out transform scale-95">
        
        <div id="modal-icon-bg" class="w-16 h-16 rounded-full flex items-center justify-center mx-auto ring-8 transition-colors">
            <i data-lucide="help-circle" id="modal-icon" class="w-8 h-8 transition-colors"></i>
        </div>
        
        <div class="space-y-1">
            <h4 id="modal-title" class="text-lg font-bold text-gray-900">Konfirmasi Tindakan</h4>
            <p class="text-sm text-gray-500">Anda akan <span id="modal-action-text" class="font-bold">memproses</span> pengajuan milik <span id="modal-staff-name" class="font-bold text-gray-900"></span>.</p>
        </div>
        
        <div class="grid grid-cols-2 gap-3 pt-2">
            <button type="button" onclick="closeConfirmModal()" class="w-full bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer">
                Batal
            </button>
            <button type="button" id="modal-submit-btn" onclick="submitAction()" class="w-full text-white font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer shadow-sm">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- JAVASCRIPT CONTROLLER FOR MODALS                          --}}
{{-- ========================================================= --}}
<script>
    let activeFormId = null;

    // --- LOGIKA MODAL SUCCESS ---
    window.addEventListener('load', () => {
        const successModal = document.getElementById('success-modal');
        if (successModal) {
            const contentBox = successModal.querySelector('div.bg-white');
            successModal.classList.remove('hidden');
            successModal.classList.add('flex');
            
            setTimeout(() => {
                successModal.classList.remove('opacity-0', 'translate-y-4');
                successModal.classList.add('opacity-100', 'translate-y-0');
                contentBox.classList.remove('scale-95');
                contentBox.classList.add('scale-100');
            }, 10); // Jeda kecil untuk memicu reflow CSS
        }
    });

    function closeSuccessModal() {
        const successModal = document.getElementById('success-modal');
        if (successModal) {
            const contentBox = successModal.querySelector('div.bg-white');
            successModal.classList.remove('opacity-100', 'translate-y-0');
            successModal.classList.add('opacity-0', 'translate-y-4');
            contentBox.classList.remove('scale-100');
            contentBox.classList.add('scale-95');
            
            setTimeout(() => {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
            }, 300);
        }
    }

    // --- LOGIKA MODAL KONFIRMASI (SETUJU / TOLAK) ---
    function openConfirmModal(action, idIzin, staffName) {
        activeFormId = `form-action-${idIzin}`;
        
        // Update input hidden form dengan tindakan ('setuju' / 'tolak')
        document.getElementById(`tindakan-${idIzin}`).value = action;
        
        // Ambil elemen modal
        const modal = document.getElementById('confirm-modal');
        const contentBox = modal.querySelector('div.bg-white');
        
        const iconBg = document.getElementById('modal-icon-bg');
        const icon = document.getElementById('modal-icon');
        const title = document.getElementById('modal-title');
        const actionText = document.getElementById('modal-action-text');
        const staffNameSpan = document.getElementById('modal-staff-name');
        const submitBtn = document.getElementById('modal-submit-btn');

        // Reset class warna
        iconBg.className = "w-16 h-16 rounded-full flex items-center justify-center mx-auto ring-8 transition-colors";
        icon.className = "w-8 h-8 transition-colors";
        submitBtn.className = "w-full text-white font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer shadow-sm";

        // Set data berdasarkan aksi
        staffNameSpan.innerText = staffName;
        
        if (action === 'setuju') {
            iconBg.classList.add('bg-emerald-50', 'ring-emerald-50/50');
            icon.classList.add('text-emerald-600');
            icon.setAttribute('data-lucide', 'check-circle-2');
            
            title.innerText = 'Setujui Izin?';
            actionText.innerText = 'MENYETUJUI';
            actionText.className = 'font-bold text-emerald-600';
            
            submitBtn.innerText = 'Ya, Setujui';
            submitBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
        } else {
            iconBg.classList.add('bg-rose-50', 'ring-rose-50/50');
            icon.classList.add('text-rose-600');
            icon.setAttribute('data-lucide', 'x-circle');
            
            title.innerText = 'Tolak Izin?';
            actionText.innerText = 'MENOLAK';
            actionText.className = 'font-bold text-rose-600';
            
            submitBtn.innerText = 'Ya, Tolak';
            submitBtn.classList.add('bg-rose-600', 'hover:bg-rose-700');
        }

        // Render ulang icon lucide
        lucide.createIcons();

        // Kunci tabel biar ga bisa diklik sembarangan
        document.getElementById('izin-table-body').classList.add('pointer-events-none');

        // Tampilkan modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.remove('opacity-0', 'translate-y-4');
            modal.classList.add('opacity-100', 'translate-y-0');
            contentBox.classList.remove('scale-95');
            contentBox.classList.add('scale-100');
        }, 10);
    }

    function closeConfirmModal() {
        const modal = document.getElementById('confirm-modal');
        const contentBox = modal.querySelector('div.bg-white');
        
        modal.classList.remove('opacity-100', 'translate-y-0');
        modal.classList.add('opacity-0', 'translate-y-4');
        contentBox.classList.remove('scale-100');
        contentBox.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('izin-table-body').classList.remove('pointer-events-none');
            activeFormId = null;
        }, 300);
    }

    function submitAction() {
        if (activeFormId) {
            document.getElementById(activeFormId).submit();
        }
    }
</script>
@endsection