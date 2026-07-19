{{-- FILE: resources/views/components/delete-attendance.blade.php --}}

<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300 opacity-0 translate-y-4">
    {{-- Background Gelap --}}
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    
    {{-- Box Modal --}}
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 z-10 overflow-hidden transform scale-95 transition-all duration-300 text-center p-6 sm:p-8">
        
        {{-- Ikon Peringatan --}}
        <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mx-auto ring-8 ring-rose-50/50 mb-5">
            <i data-lucide="alert-triangle" class="w-8 h-8 text-rose-600 animate-pulse"></i>
        </div>

        {{-- Teks Konfirmasi --}}
        <h4 class="text-xl font-black text-gray-900 tracking-tight mb-2">Hapus Data Absensi?</h4>
        <p class="text-sm text-gray-500 leading-relaxed mb-6">
            Kamu yakin ingin menghapus data presensi atas nama <span id="delete-user-name" class="font-bold text-gray-800">-</span> pada tanggal <span id="delete-date" class="font-bold text-gray-800">-</span>? <br>
            <span class="text-rose-500 text-xs font-semibold mt-1 inline-block">Tindakan ini tidak dapat dibatalkan.</span>
        </p>

        {{-- Form & Tombol Aksi --}}
        <form id="delete-form" method="POST" class="grid grid-cols-2 gap-3">
            @csrf
            @method('DELETE')
            
            <button type="button" onclick="closeDeleteModal()" 
                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-xl text-sm transition-colors cursor-pointer">
                Batal
            </button>
            
            <button type="submit" 
                class="w-full inline-flex items-center justify-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 px-4 rounded-xl text-sm shadow-md shadow-rose-600/20 transition-all cursor-pointer">
                <i data-lucide="trash-2" class="w-4 h-4"></i> Ya, Hapus
            </button>
        </form>

    </div>
</div>

{{-- Script Khusus Mengatur Modal Hapus --}}
<script>
    function openDeleteModal(id, name, date) {
        const modal = document.getElementById('delete-modal');
        const box = modal.querySelector('div.bg-white');
        const form = document.getElementById('delete-form');

        // Isi data nama dan tanggal di dalam teks modal
        document.getElementById('delete-user-name').textContent = name || '-';
        document.getElementById('delete-date').textContent = date || '-';

        // Ubah action URL form menuju route destroy admin
        form.action = `/admin/attendance/${id}`;

        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Animasi muncul
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0', 'translate-y-4');
            modal.classList.add('opacity-100', 'translate-y-0');
            box.classList.remove('scale-95');
            box.classList.add('scale-100');
        }, 10);
    }

    function closeDeleteModal() {
        const modal = document.getElementById('delete-modal');
        const box = modal.querySelector('div.bg-white');

        modal.classList.remove('opacity-100', 'translate-y-0');
        modal.classList.add('opacity-0', 'translate-y-4');
        box.classList.remove('scale-100');
        box.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
</script>