<button id="sidebar-toggle" onclick="toggleSidebar()"
    class="lg:hidden fixed left-5 top-5 z-40 w-11 h-11 rounded-xl bg-white border border-gray-200 shadow-lg flex items-center justify-center hover:bg-gray-50 transition">
    <i data-lucide="menu" class="text-gray-700"></i>
</button>

<div id="sidebar-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"
    onclick="toggleSidebar()">
</div>

<aside id="sidebar"
    class="fixed inset-y-0 left-0 w-72 bg-white border-r border-gray-200 shadow-xl z-50 flex flex-col transform transition-transform duration-300 -translate-x-full lg:translate-x-0">

    <div class="flex-1 overflow-y-auto p-5 mt-14 lg:mt-0">
        <p class="text-[11px] uppercase tracking-widest text-gray-400 mb-4 px-2">Main Menu</p>

        <nav class="space-y-1.5">
            {{-- Dashboard --}}
            <a href="{{ route('admin.index') }}"
                class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 
               {{ request()->routeIs('admin.index') ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                <div
                    class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ request()->routeIs('admin.index') ? 'bg-amber-100' : 'bg-gray-100 group-hover:bg-amber-100' }}">
                    <i data-lucide="layout-dashboard"
                        class="w-5 h-5 {{ request()->routeIs('admin.index') ? 'text-amber-700' : 'text-gray-500' }}"></i>
                </div>
                Dashboard
            </a>

            {{-- Staff --}}
            <a href="{{ route('admin.staff.index') }}"
                class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 
               {{ request()->routeIs('admin.staff.*') ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                <div
                    class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ request()->routeIs('admin.staff.*') ? 'bg-amber-100' : 'bg-gray-100 group-hover:bg-amber-100' }}">
                    <i data-lucide="users"
                        class="w-5 h-5 {{ request()->routeIs('admin.staff.*') ? 'text-amber-700' : 'text-gray-500' }}"></i>
                </div>
                Manajemen Staff
            </a>

            {{-- Izin --}}
            <a href="{{ route('admin.izin.index') }}"
                class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 
               {{ request()->routeIs('admin.izin.*') ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                <div
                    class="w-10 h-10 rounded-xl bg-gray-100 group-hover:bg-amber-100 flex items-center justify-center shrink-0">
                    <i data-lucide="calendar-days" class="w-5 h-5 text-gray-500"></i>
                </div>
                <span>Persetujuan Izin</span>
                @php
                    // Ngambil jumlah data izin yang statusnya masih 'pending'
                    $pendingIzin = \App\Models\LeaveRequest::where('status', 'pending')->count();
                @endphp

                {{-- Badge ini cuma bakal nongol kalau ada data yang belum diproses (> 0) --}}
                @if ($pendingIzin > 0)
                    <span
                        class="ml-auto min-w-[24px] px-1.5 h-6 flex items-center justify-center rounded-full bg-rose-500 text-white text-[10px] font-black shadow-md animate-pulse">
                        {{ $pendingIzin }}
                    </span>
                @endif
            </a>

            {{-- Statistik --}}
            <a href="#"
                class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 
               {{ request()->routeIs('admin.statistik.*') ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                <div
                    class="w-10 h-10 rounded-xl bg-gray-100 group-hover:bg-amber-100 flex items-center justify-center shrink-0">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 text-gray-500"></i>
                </div>
                Statistik
            </a>
        </nav>
    </div>

    {{-- Footer --}}
    <div class="border-t border-gray-100 p-5 bg-gray-50/50">
        <div class="flex items-center gap-3 mb-4">
            <div
                class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center shrink-0 border border-amber-200">
                <i data-lucide="user" class="text-amber-600"></i>
            </div>
            <div class="overflow-hidden">
                <p class="font-semibold text-sm truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-bold">Admin</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                class="w-full rounded-xl border border-red-200 py-3 text-red-500 hover:bg-red-50 hover:border-red-300 transition flex items-center justify-center gap-2 font-medium">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                Logout
            </button>
        </form>
    </div>
</aside>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    function toggleSidebar() {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.add('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    });

    lucide.createIcons();
</script>
