@extends('dashboard.admin.index')

@section('content')
    <x-edit-staff />
    <div class="space-y-6 pt-16 lg:pt-0 max-w-[1600px] mx-auto antialiased">

        <div class="space-y-1">
            <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Manajemen Registrasi & Data Staff</h2>
            <p class="text-sm text-gray-500">Kelola akun pengguna, berikan akses role, dan hapus staff yang sudah tidak
                aktif.</p>
        </div>

        {{-- Error Alert Banner (Hanya untuk validasi input yang gagal) --}}
        @if (session('error') || $errors->any())
            <div
                class="p-4 rounded-xl border bg-rose-50 border-rose-200 text-rose-800 text-sm font-semibold flex items-center gap-2 animate-fade-in">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                <div>
                    {{ session('error') }}
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm h-fit">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-5 flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4 text-amber-500"></i> Registrasi Akun Baru
                </h3>

                <form action="{{ route('admin.staff.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso"
                            required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="Contoh: budi.s"
                            required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="budi@ypp.or.id"
                            required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Password</label>
                        <div class="relative">

                            <input type="password" name="password" id="reg-password" placeholder="Minimal 6 karakter"
                                required minlength="6"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                            <button type="button" onclick="toggleRegPassword()"
                                class="absolute right-3 top-3 text-gray-400 hover:text-amber-600">
                                <i data-lucide="eye" id="reg-eye-icon" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Akses Role (Spatie)</label>
                        <select name="role" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors cursor-pointer">
                            <option value="" disabled selected>-- Pilih Role --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 rounded-xl text-sm transition-all mt-2 shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Staff
                    </button>
                </form>
            </div>

            <div
                class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col relative">

                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                        <i data-lucide="users" class="w-4 h-4 text-gray-400"></i> Daftar Akun Terdaftar
                    </h3>
                    <span class="text-xs font-bold bg-gray-200 text-gray-600 px-2 py-1 rounded-md">{{ $users->count() }}
                        Akun</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead
                            class="bg-gray-50/80 text-[11px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Informasi Staff</th>
                                <th class="px-6 py-4">Username / Email</th>
                                <th class="px-6 py-4">Role Spatie</th>
                                <th class="px-6 py-4 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody id="staff-table-body" class="divide-y divide-gray-100 text-sm">
                            @forelse ($users as $user)
                                <tr class="hover:bg-gray-50/50 transition-colors group">

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 rounded-full bg-gradient-to-tr from-amber-100 to-orange-50 flex items-center justify-center text-amber-700 font-extrabold text-xs shrink-0 ring-2 ring-white shadow-sm">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span
                                                class="font-bold text-gray-900 group-hover:text-amber-600 transition-colors">{{ $user->name }}</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-gray-900 font-medium text-xs mb-0.5">{{ $user->username }}</p>
                                        <p class="text-gray-500 text-xs">{{ $user->email ?? '-' }}</p>
                                    </td>

                                    <td class="px-6 py-4">
                                        @foreach ($user->roles as $role)
                                            @php
                                                $color = match ($role->name) {
                                                    'admin' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                                    'manager' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                                    default => 'bg-green-50 text-green-700 ring-green-600/20',
                                                };
                                            @endphp
                                            <span
                                                class="{{ $color }} px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider ring-1 inset-ring inline-block mb-1 mr-1">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                        @if ($user->roles->isEmpty())
                                            <span class="text-gray-400 text-xs italic">No Role</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 ActionsColumn">
                                        <div class="flex justify-center gap-2">
                                            <button type="button" onclick="openEditModal(@js($user))"
                                                class="text-blue-600 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors cursor-pointer"
                                                title="Edit">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </button>

                                            @if ($user->id !== auth()->id())
                                                <form action="{{ route('admin.staff.destroy', $user->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        onclick="openDeleteModal(this, '{{ $user->name }}')"
                                                        class="text-rose-600 bg-rose-50 hover:bg-rose-100 p-2 rounded-lg transition-colors cursor-pointer"
                                                        title="Hapus">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-300 bg-gray-50 p-2 rounded-lg"
                                                    title="Tidak bisa hapus akun sendiri">
                                                    <i data-lucide="shield-alert" class="w-4 h-4"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-16 text-center">
                                        <div class="flex flex-col items-center text-gray-400">
                                            <i data-lucide="users-x" class="w-8 h-8 mb-2 opacity-50"></i>
                                            <p class="text-sm font-medium text-gray-900">Belum ada staff</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 1: POPUP SUCCESS (Fade-In-Down & Blur Animation)    --}}
    {{-- ========================================================= --}}
    @if (session('success'))
        <div id="success-modal"
            class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300 ease-out opacity-0 translate-y-4">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeSuccessModal()"></div>

            <div
                class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-gray-100 z-10 text-center space-y-4 transition-all duration-300 ease-out transform scale-95">
                <div
                    class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto ring-8 ring-emerald-50/50">
                    <i data-lucide="check" class="w-8 h-8 text-emerald-600"></i>
                </div>
                <div class="space-y-1">
                    <h4 class="text-lg font-bold text-gray-900">Berhasil!</h4>
                    <p class="text-sm text-gray-500">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="closeSuccessModal()"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer shadow-sm">
                    Selesai
                </button>
            </div>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- MODAL 2: POPUP KONFIRMASI HAPUS (Smooth CSS Transition)    --}}
    {{-- ========================================================= --}}
    <div id="delete-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300 ease-in-out opacity-0 translate-y-4">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDeleteModal()"></div>

        <div
            class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-gray-100 z-10 text-center space-y-4 transition-all duration-300 ease-in-out transform scale-95">
            <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mx-auto ring-8 ring-rose-50/50">
                <i data-lucide="alert-triangle" class="w-8 h-8 text-rose-600"></i>
            </div>
            <div class="space-y-1">
                <h4 class="text-lg font-bold text-gray-900">Hapus Akun Staff?</h4>
                <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus akun milik <span id="delete-staff-name"
                        class="font-bold text-gray-900"></span>? Tindakan ini permanen.</p>
            </div>
            <div class="grid grid-cols-2 gap-3 pt-2">
                <button type="button" onclick="closeDeleteModal()"
                    class="w-full bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="button" onclick="submitDelete()"
                    class="w-full bg-rose-600 hover:bg-rose-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer shadow-sm">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- JAVASCRIPT CONTROLLER FOR SMOOTH TRANSITIONS              --}}
    {{-- ========================================================= --}}
    <script>
        // Variabel global untuk menampung form yang sedang ditarget
        let activeDeleteForm = null;

        // --- LOGIKA MODAL SUCCESS (Auto-Trigger on Load) ---
        window.addEventListener('load', () => {
            const successModal = document.getElementById('success-modal');
            if (successModal) {
                const contentBox = successModal.querySelector('div.bg-white');
                // Tampilkan elemennya dulu (tapi masih transparan)
                successModal.classList.remove('hidden');
                successModal.classList.add('flex');

                // Beri jeda 1ms agar CSS mendaftarkan status flex, lalu picu animasi masuk
                setTimeout(() => {
                    successModal.classList.remove('opacity-0', 'translate-y-4');
                    successModal.classList.add('opacity-100', 'translate-y-0');
                    contentBox.classList.remove('scale-95');
                    contentBox.classList.add('scale-100');
                }, 1);
            }
        });

        function closeSuccessModal() {
            const successModal = document.getElementById('success-modal');
            if (successModal) {
                const contentBox = successModal.querySelector('div.bg-white');
                // Picu animasi keluar (Fade out & Scale Down)
                successModal.classList.remove('opacity-100', 'translate-y-0');
                successModal.classList.add('opacity-0', 'translate-y-4');
                contentBox.classList.remove('scale-100');
                contentBox.classList.add('scale-95');

                // Tunggu animasi CSS selesai (300ms) baru sembunyikan elemen sepenuhnya
                setTimeout(() => {
                    successModal.classList.add('hidden');
                    successModal.classList.remove('flex');
                }, 300);
            }
        }

        // --- LOGIKA MODAL HAPUS (Pemicu JavaScript) ---
        function openDeleteModal(button, staffName) {
            activeDeleteForm = button.closest('form');
            document.getElementById('delete-staff-name').innerText = staffName;

            const deleteModal = document.getElementById('delete-modal');
            const contentBox = deleteModal.querySelector('div.bg-white');

            // Nonaktifkan 'group hover' di baris tabel sementara agar UI tidak kacau
            document.getElementById('staff-table-body').classList.add('pointer-events-none');

            // Tampilkan elemen dan picu animasi masuk
            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');

            setTimeout(() => {
                deleteModal.classList.remove('opacity-0', 'translate-y-4');
                deleteModal.classList.add('opacity-100', 'translate-y-0');
                contentBox.classList.remove('scale-95');
                contentBox.classList.add('scale-100');
            }, 1);
        }

        function closeDeleteModal() {
            const deleteModal = document.getElementById('delete-modal');
            const contentBox = deleteModal.querySelector('div.bg-white');

            // Picu animasi keluar
            deleteModal.classList.remove('opacity-100', 'translate-y-0');
            deleteModal.classList.add('opacity-0', 'translate-y-4');
            contentBox.classList.remove('scale-100');
            contentBox.classList.add('scale-95');

            // Tunggu animasi CSS selesai baru sembunyikan elemen dan aktifkan kembali hover tabel
            setTimeout(() => {
                deleteModal.classList.add('hidden');
                deleteModal.classList.remove('flex');
                document.getElementById('staff-table-body').classList.remove('pointer-events-none');
                activeDeleteForm = null;
            }, 300);
        }

        function submitDelete() {
            if (activeDeleteForm) {
                // Langsung submit, animasi penutup tidak perlu karena halaman akan reload
                activeDeleteForm.submit();
            }
        }
        // Fungsi untuk membuka Modal Edit
        function openEditModal(user) {
            const editModal = document.getElementById('edit-modal');
            const contentBox = editModal.querySelector('div.bg-white');

            // Set Action Form
            document.getElementById('edit-form').action = `/admin/staff/${user.id}`;

            // Isi data text
            document.getElementById('edit-name').value = user.name;
            document.getElementById('edit-email').value = user.email;
            document.getElementById('edit-username').value = user.username;

            // LOGIKA SET ROLE:
            // User bisa punya banyak role di Spatie, tapi biasanya kita ambil role pertama (index 0)
            if (user.roles && user.roles.length > 0) {
                document.getElementById('edit-role').value = user.roles[0].name;
            } else {
                document.getElementById('edit-role').value = ""; // Default jika tidak ada role
            }

            // Animasi muncul... (sisanya sama)
            editModal.classList.remove('hidden');
            editModal.classList.add('flex');
            setTimeout(() => {
                editModal.classList.remove('opacity-0', 'translate-y-4');
                editModal.classList.add('opacity-100', 'translate-y-0');
                contentBox.classList.remove('scale-95');
                contentBox.classList.add('scale-100');
            }, 1);
        }


        function closeEditModal() {
            const editModal = document.getElementById('edit-modal');
            const contentBox = editModal.querySelector('div.bg-white');

            editModal.classList.remove('opacity-100', 'translate-y-0');
            editModal.classList.add('opacity-0', 'translate-y-4');
            contentBox.classList.remove('scale-100');
            contentBox.classList.add('scale-95');

            setTimeout(() => {
                editModal.classList.add('hidden');
                editModal.classList.remove('flex');
            }, 300);
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('edit-password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                // Ganti icon ke eye-off
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = "password";
                // Ganti icon kembali ke eye
                eyeIcon.setAttribute('data-lucide', 'eye');
            }

            // Wajib panggil ini agar Lucide me-render ulang icon yang atributnya baru diganti
            lucide.createIcons();
        }
    </script>
    <script>
        // Toggle Password Registrasi
        function toggleRegPassword() {
            const input = document.getElementById('reg-password');
            const icon = document.getElementById('reg-eye-icon');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.setAttribute('data-lucide', input.type === 'password' ? 'eye' : 'eye-off');
            lucide.createIcons();
        }

        // Modal Konfirmasi Hapus
        function confirmDelete(id, name) {
            if (confirm(`Yakin ingin menghapus staff: ${name}?`)) {
                // Jalankan form delete via JS atau buat form hidden
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/staff/${id}`;
                form.innerHTML = '@csrf @method('DELETE')';
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

@endsection
