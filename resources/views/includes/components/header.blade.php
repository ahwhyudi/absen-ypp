<header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-28 h-10 flex items-center justify-center overflow-hidden shrink-0">
                    <img  alt="Logo YPP" class="object-contain w-full h-full"
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
                <a href="#" class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-rose-500 transition-colors font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/></svg>
                    <span class="hidden sm:inline">Keluar</span>
                </a>
            </div>
        </div>
    </header>