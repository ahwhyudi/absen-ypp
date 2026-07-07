<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengajuan Izin / Cuti - YPP</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gray-50 font-sans min-h-screen flex flex-col">

    @include('includes.components.header')
    <main class="flex-1 flex items-center justify-center p-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 max-w-md w-full">
            <div class="mb-6">
                <h1 class="text-xl font-bold text-gray-900">Pengajuan Izin / Sakit / Cuti</h1>
                <p class="text-xs text-gray-500 mt-1">Halo <b>{{ auth()->user()->name }}</b>, isi formulir ini dengan
                    data yang valid.</p>
            </div>

            @if (session('success'))
                <div
                    class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl text-sm font-semibold mb-5 flex items-center gap-2">
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

            <form id="formIzin" action="{{ route('leave-request.store') }}" method="POST"
                enctype="multipart/form-data" class="space-y-4">

                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">Jenis Pengajuan</label>
                    <select name="jenis_izin" required
                        class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="Izin">Izin Keperluan Mendesak</option>
                        <option value="Sakit">Sakit (Butuh Istirahat)</option>
                        <option value="Cuti">Cuti Tahunan Staff</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">Alasan / Keterangan</label>
                    <textarea name="keterangan" rows="4" required placeholder="Tulis alasan yang jelas dan detail..."
                        class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">
                        Dokumen Pendukung <span class="text-gray-400 normal-case font-normal">(Opsional: Surat Dokter,
                            dll.)</span>
                    </label>
                    <div class="relative">
                        <input type="file" name="dokumen_pendukung" id="inputDokumen" accept=".jpg,.jpeg,.png,.pdf"
                            onchange="previewFile(this)"
                            class="w-full bg-gray-50 border border-dashed border-gray-300 text-gray-700 text-sm rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 cursor-pointer">
                    </div>
                    <p id="preview-nama"
                        class="text-xs text-emerald-600 font-medium mt-1 hidden flex items-center gap-1">
                        <i data-lucide="check-circle-2" class="w-3 h-3"></i>
                        <span id="preview-teks"></span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, atau PDF. Maks 5MB.</p>
                </div>

                <button type="button" onclick="konfirmasiKirim()"
                    class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-md text-sm cursor-pointer flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i> Kirim Permohonan
                </button>
            </form>
        </div>
    </main>

    @include('includes.components.navbar')

    <footer class="text-center py-3 text-xs text-gray-400 bg-white border-t border-gray-100">
        &copy; 2026 Yayasan Prasasti Perdamaian — Semua Hak Dilindungi.
    </footer>

    <script>
        lucide.createIcons();

        function previewFile(input) {
            const preview = document.getElementById('preview-nama');
            const teks = document.getElementById('preview-teks');
            if (input.files && input.files[0]) {
                preview.classList.remove('hidden');
                teks.textContent = input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(1) + ' KB)';
                lucide.createIcons();
            }
        }

        function konfirmasiKirim() {
            const jenis = document.querySelector('[name="jenis_izin"]').value;
            const mulai = document.querySelector('[name="tanggal_mulai"]').value;
            const selesai = document.querySelector('[name="tanggal_selesai"]').value;

            if (!mulai || !selesai) {
                alert('Mohon lengkapi tanggal mulai dan selesai.');
                return;
            }

            const konfirmasi = confirm(
                `Konfirmasi Pengajuan:\n\n` +
                `Jenis: ${jenis}\n` +
                `Periode: ${mulai} s/d ${selesai}\n\n` +
                `Apakah data sudah benar? Klik OK untuk mengirim.`
            );
            if (konfirmasi) {
                document.getElementById('formIzin').submit();
            }
        }
    </script>
</body>

</html>
