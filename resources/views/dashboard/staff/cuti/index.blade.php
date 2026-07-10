<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengajuan Izin / Cuti - YPP</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gray-50 font-sans min-h-screen flex flex-col relative">

    @include('includes.components.header')
    
    <main class="flex-1 flex items-center justify-center p-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 max-w-md w-full relative z-0">
            <div class="mb-6">
                <h1 class="text-xl font-bold text-gray-900">Pengajuan Izin / Sakit / Cuti</h1>
                <p class="text-xs text-gray-500 mt-1">Halo <b>{{ auth()->user()->name }}</b>, isi formulir ini dengan data yang valid.</p>
            </div>

            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl text-sm font-semibold mb-5 flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                    <ul class="text-sm text-red-700 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="formIzin" action="{{ route('leave-request.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">Jenis Pengajuan</label>
                    <select name="jenis_izin" id="input_jenis" required class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 cursor-pointer">
                        <option value="Izin">Izin Keperluan Mendesak</option>
                        <option value="Sakit">Sakit (Butuh Istirahat)</option>
                        <option value="Cuti">Cuti Tahunan Staff</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="input_mulai" required class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="input_selesai" required class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500 cursor-pointer">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">Alasan / Keterangan</label>
                    <textarea name="keterangan" rows="4" required placeholder="Tulis alasan yang jelas dan detail..." class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">
                        Dokumen Pendukung <span class="text-gray-400 normal-case font-normal">(Opsional: Surat Dokter, dll.)</span>
                    </label>
                    <div class="relative">
                        <input type="file" name="dokumen_pendukung" id="inputDokumen" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this)" class="w-full bg-gray-50 border border-dashed border-gray-300 text-gray-700 text-sm rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 cursor-pointer transition-colors">
                    </div>
                    <p id="preview-nama" class="text-xs text-emerald-600 font-medium mt-1 hidden items-center gap-1">
                        <i data-lucide="check-circle-2" class="w-3 h-3"></i>
                        <span id="preview-teks"></span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, atau PDF. Maks 5MB.</p>
                </div>

                <button type="button" onclick="konfirmasiKirim()" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-md text-sm cursor-pointer flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i> Kirim Permohonan
                </button>
            </form>
        </div>
    </main>

    @include('includes.components.navbar')

    <footer class="text-center py-3 text-xs text-gray-400 bg-white border-t border-gray-100">
        &copy; 2026 Yayasan Prasasti Perdamaian — Semua Hak Dilindungi.
    </footer>

    {{-- ========================================================= --}}
    {{-- MODAL 1: ALERT VALIDASI GAGAL                             --}}
    {{-- ========================================================= --}}
    <div id="alert-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300 ease-in-out opacity-0 translate-y-4">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeAlertModal()"></div>
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-gray-100 z-10 text-center transform scale-95 transition-all duration-300 ease-in-out">
            <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mx-auto ring-8 ring-rose-50/50 mb-4">
                <i data-lucide="alert-triangle" class="w-8 h-8 text-rose-600"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-900 mb-1">Data Belum Lengkap!</h4>
            <p class="text-sm text-gray-500 mb-6">Mohon lengkapi Tanggal Mulai dan Tanggal Selesai sebelum mengirim permohonan.</p>
            <button type="button" onclick="closeAlertModal()" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer shadow-sm">
                Mengerti
            </button>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 2: KONFIRMASI KIRIM                                 --}}
    {{-- ========================================================= --}}
    <div id="confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300 ease-in-out opacity-0 translate-y-4">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeConfirmModal()"></div>
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-gray-100 z-10 transform scale-95 transition-all duration-300 ease-in-out text-center">
            
            <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto ring-8 ring-amber-50/50 mb-4">
                <i data-lucide="send" class="w-8 h-8 text-amber-600"></i>
            </div>
            
            <h4 class="text-lg font-bold text-gray-900 mb-1">Kirim Permohonan?</h4>
            <p class="text-sm text-gray-500 mb-4">Pastikan rentang tanggal dan jenis pengajuan Anda sudah benar.</p>
            
            <div class="bg-gray-50 rounded-xl p-3.5 mb-6 text-left text-sm border border-gray-100 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 font-medium text-xs uppercase tracking-wider">Jenis:</span>
                    <span id="modal-jenis" class="font-bold text-gray-900 bg-white px-2 py-1 rounded shadow-sm border border-gray-100"></span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                    <span class="text-gray-500 font-medium text-xs uppercase tracking-wider">Periode:</span>
                    <span id="modal-periode" class="font-bold text-amber-600 text-right"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="closeConfirmModal()" class="w-full bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="button" onclick="submitFormIzin()" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer shadow-sm">
                    Ya, Kirim
                </button>
            </div>
        </div>
    </div>

    <script>
        // Inisialisasi Icon Lucide
        lucide.createIcons();

        // Fitur Preview File
        function previewFile(input) {
            const preview = document.getElementById('preview-nama');
            const teks = document.getElementById('preview-teks');
            if (input.files && input.files[0]) {
                preview.classList.remove('hidden');
                preview.classList.add('flex');
                teks.textContent = input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(1) + ' KB)';
                lucide.createIcons();
            } else {
                preview.classList.add('hidden');
                preview.classList.remove('flex');
            }
        }

        // ==========================================
        // LOGIKA MODAL (PENGGANTI ALERT & CONFIRM)
        // ==========================================

        function konfirmasiKirim() {
            const jenis = document.getElementById('input_jenis').value;
            const mulai = document.getElementById('input_mulai').value;
            const selesai = document.getElementById('input_selesai').value;

            // Jika tanggal kosong, tampilkan Modal Alert (bukan alert bawaan)
            if (!mulai || !selesai) {
                const alertModal = document.getElementById('alert-modal');
                const contentBox = alertModal.querySelector('div.bg-white');
                
                alertModal.classList.remove('hidden');
                alertModal.classList.add('flex');
                setTimeout(() => {
                    alertModal.classList.remove('opacity-0', 'translate-y-4');
                    alertModal.classList.add('opacity-100', 'translate-y-0');
                    contentBox.classList.remove('scale-95');
                    contentBox.classList.add('scale-100');
                }, 10);
                return;
            }

            // Jika form lengkap, isi data ke Modal Konfirmasi
            document.getElementById('modal-jenis').innerText = jenis;
            
            // Format tanggal agar lebih rapi (opsional, tapi lebih estetik)
            const formatTgl = (tgl) => tgl.split('-').reverse().join('/');
            document.getElementById('modal-periode').innerHTML = `${formatTgl(mulai)} <span class="text-gray-300 mx-1">➜</span> ${formatTgl(selesai)}`;

            // Tampilkan Modal Konfirmasi
            const confirmModal = document.getElementById('confirm-modal');
            const confirmBox = confirmModal.querySelector('div.bg-white');
            
            confirmModal.classList.remove('hidden');
            confirmModal.classList.add('flex');
            setTimeout(() => {
                confirmModal.classList.remove('opacity-0', 'translate-y-4');
                confirmModal.classList.add('opacity-100', 'translate-y-0');
                confirmBox.classList.remove('scale-95');
                confirmBox.classList.add('scale-100');
            }, 10);
        }

        function closeAlertModal() {
            const alertModal = document.getElementById('alert-modal');
            const contentBox = alertModal.querySelector('div.bg-white');
            
            alertModal.classList.remove('opacity-100', 'translate-y-0');
            alertModal.classList.add('opacity-0', 'translate-y-4');
            contentBox.classList.remove('scale-100');
            contentBox.classList.add('scale-95');
            
            setTimeout(() => {
                alertModal.classList.add('hidden');
                alertModal.classList.remove('flex');
            }, 300);
        }

        function closeConfirmModal() {
            const confirmModal = document.getElementById('confirm-modal');
            const contentBox = confirmModal.querySelector('div.bg-white');
            
            confirmModal.classList.remove('opacity-100', 'translate-y-0');
            confirmModal.classList.add('opacity-0', 'translate-y-4');
            contentBox.classList.remove('scale-100');
            contentBox.classList.add('scale-95');
            
            setTimeout(() => {
                confirmModal.classList.add('hidden');
                confirmModal.classList.remove('flex');
            }, 300);
        }

        // Fungsi Submit dipanggil saat tombol "Ya, Kirim" di Modal ditekan
        function submitFormIzin() {
            document.getElementById('formIzin').submit();
        }
    </script>
</body>

</html>