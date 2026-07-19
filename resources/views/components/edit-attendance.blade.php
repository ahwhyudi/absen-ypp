{{-- FILE: resources/views/components/edit-attendance.blade.php --}}

<div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300 opacity-0 translate-y-4">
    {{-- Background Gelap --}}
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeEditModal()"></div>
    
    {{-- Box Modal --}}
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-gray-100 z-10 overflow-hidden transform scale-95 transition-all duration-300">
        
        {{-- Header Modal: Nama & Jabatan --}}
        <div class="bg-gradient-to-r from-gray-900 to-slate-800 p-6 text-white flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-amber-400 font-extrabold text-lg shrink-0 ring-1 ring-white/20">
                    <i data-lucide="user-check" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 id="modal-user-name" class="text-lg font-bold tracking-tight text-white">-</h4>
                    <span id="modal-user-role" class="inline-block px-2 py-0.5 mt-0.5 text-[11px] font-semibold bg-amber-500/20 text-amber-300 rounded-md uppercase tracking-wider border border-amber-500/30">-</span>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- Form Edit --}}
        <form id="edit-form" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Jam Masuk & Pulang --}}
            <div class="space-y-3">
               {{-- ========================================================= --}}
        {{-- SECTION INPUT JAM KEKINIAN (MODERN SAAS STYLE) --}}
        {{-- ========================================================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-6">
            
            {{-- 1. KARTU JAM MASUK --}}
            <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 focus-within:border-indigo-500 focus-within:ring-4 focus-within:ring-indigo-500/10 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                        <i data-lucide="log-in" class="w-3.5 h-3.5 text-indigo-600"></i> Jam Masuk
                    </label>
                    <span class="text-[10px] font-semibold px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-full">In</span>
                </div>
                
                {{-- Input Time Modern (Tanpa Detik) --}}
                <input type="time" name="check_in" id="input-check-in" 
                    class="w-full bg-transparent text-2xl font-black text-slate-800 focus:outline-none cursor-pointer tracking-tight"
                    >
            </div>

            {{-- 2. KARTU JAM PULANG --}}
            <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-500/10 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                        <i data-lucide="log-out" class="w-3.5 h-3.5 text-emerald-600"></i> Jam Pulang
                    </label>
                    <span class="text-[10px] font-semibold px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-full">Out</span>
                </div>
                
                {{-- Input Time Modern (Tanpa Detik) --}}
                <input type="time" name="check_out" id="input-check-out" 
                    class="w-full bg-transparent text-2xl font-black text-slate-800 focus:outline-none cursor-pointer tracking-tight">
            </div>

        </div>
            </div>

            <hr class="border-gray-100">

            {{-- Koordinat GPS --}}
            <div class="space-y-3">
                <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-500"></i> Edit Titik Koordinat GPS
                </h5>
                
                <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 space-y-2">
                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Koordinat Masuk</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-0.5">Latitude In</label>
                            <input type="text" name="latitude_in" id="input-lat-in" placeholder="-6.123456"
                                class="w-full bg-white border border-gray-200 text-gray-800 text-xs font-mono rounded-lg p-2 focus:outline-none focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-0.5">Longitude In</label>
                            <input type="text" name="longitude_in" id="input-long-in" placeholder="106.123456"
                                class="w-full bg-white border border-gray-200 text-gray-800 text-xs font-mono rounded-lg p-2 focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 space-y-2">
                    <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block">Koordinat Pulang</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-0.5">Latitude Out</label>
                            <input type="text" name="latitude_out" id="input-lat-out" placeholder="-6.123456"
                                class="w-full bg-white border border-gray-200 text-gray-800 text-xs font-mono rounded-lg p-2 focus:outline-none focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-0.5">Longitude Out</label>
                            <input type="text" name="longitude_out" id="input-long-out" placeholder="106.123456"
                                class="w-full bg-white border border-gray-200 text-gray-800 text-xs font-mono rounded-lg p-2 focus:outline-none focus:border-amber-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Action --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeEditModal()" 
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl text-sm shadow-sm transition-all cursor-pointer">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script Khusus Mengatur Modal --}}
<script>
    function openEditModal(id, name, role, checkIn, checkOut, latIn, longIn, latOut, longOut) {
        const modal = document.getElementById('edit-modal');
        const box = modal.querySelector('div.bg-white');
        const form = document.getElementById('edit-form');

        // Isi data ke dalam modal
        document.getElementById('modal-user-name').textContent = name || '-';
        document.getElementById('modal-user-role').textContent = role || 'Staff';
        document.getElementById('input-check-in').value = checkIn ? checkIn.substring(0, 8) : '';
        document.getElementById('input-check-out').value = checkOut ? checkOut.substring(0, 8) : '';
        document.getElementById('input-lat-in').value = latIn || '';
        document.getElementById('input-long-in').value = longIn || '';
        document.getElementById('input-lat-out').value = latOut || '';
        document.getElementById('input-long-out').value = longOut || '';

        // Ubah action URL form sesuai ID absensi
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

    function closeEditModal() {
        const modal = document.getElementById('edit-modal');
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