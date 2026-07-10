<div id="edit-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300 ease-in-out opacity-0 translate-y-4">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeEditModal()"></div>

    <div
        class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl border border-gray-100 z-10 transform transition-all duration-300 ease-in-out scale-95">
        <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i data-lucide="edit-3" class="w-5 h-5 text-amber-500"></i> Edit Data Staff
        </h4>

        <form id="edit-form" action="" method="POST" class="space-y-3">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Nama Lengkap</label>
                <input type="text" id="edit-name" name="name" required
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Username</label>
                <input type="text" id="edit-username" name="username" required
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Email</label>
                <input type="email" id="edit-email" name="email" required
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Password Baru</label>
                <div class="relative">
                    <input type="password" id="edit-password" name="password"
                        placeholder="Kosongkan jika tidak ingin diubah"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm"
                        autocomplete="new-password">
                    <button type="button" onclick="togglePasswordVisibility()"
                        class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                        <i data-lucide="eye" id="eye-icon" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Role</label>
                <select id="edit-role" name="role" required
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm cursor-pointer">
                    <option value="" disabled>-- Pilih Role --</option>
                    @foreach (\Spatie\Permission\Models\Role::all() as $role)
                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pt-4 flex gap-3">
                <button type="button" onclick="closeEditModal()"
                    class="flex-1 bg-gray-50 hover:bg-gray-100 font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer">Batal</button>
                <button type="submit"
                    class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2.5 rounded-xl text-sm shadow-sm transition-colors cursor-pointer">Simpan
                    Perubahan</button>
            </div>
        </form>
    </div>
</div>
