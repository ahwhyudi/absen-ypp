<header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-28 h-10 flex items-center justify-center overflow-hidden shrink-0">
                    <img src="{{ asset('images/ypp.png') }}"  alt="Logo YPP" class="object-contain w-full h-full"
                         onerror="this.outerHTML='<div class=\'flex items-center justify-center w-10 h-10 bg-amber-500 rounded-xl text-white font-extrabold text-lg\'>Y</div>'">
                </div>
                <div class="border-l border-gray-200 pl-3 hidden sm:block">
                    <h1 class="text-xs font-bold text-gray-900 tracking-tight uppercase">Sistem Presensi Geotagging</h1>
                    <p class="text-[10px] text-gray-400">Yayasan Prasasti Perdamaian</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-emerald-600 font-medium flex items-center gap-1 justify-end">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full inline-block"></span> Hak Akses: {{auth()->user()->getRoleNames()->first()}}
                    </p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
            </div>
        </div>
    </header>