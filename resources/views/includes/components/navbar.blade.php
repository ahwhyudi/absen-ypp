<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-2 flex justify-around items-center z-40 shadow-lg sm:hidden">

    <a href="{{ route('attendance.index') }}"
       class="flex flex-col items-center gap-0.5 {{ request()->routeIs('attendance.index') ? 'text-amber-600' : 'text-gray-400' }}">
        <i data-lucide="scan-face" class="w-5 h-5"></i>
        <span class="text-[10px] font-medium">Absensi</span>
    </a>

    <a href="{{ route('attendance.history') }}"
       class="flex flex-col items-center gap-0.5 {{ request()->routeIs('attendance.history') ? 'text-amber-600' : 'text-gray-400' }}">
        <i data-lucide="calendar-check" class="w-5 h-5"></i>
        <span class="text-[10px] font-medium">Riwayat</span>
    </a>

    <a href="{{ route('attendance.leave.status') }}"
       class="flex flex-col items-center gap-0.5 {{ request()->routeIs('attendance.leave.status') ? 'text-amber-600' : 'text-gray-400' }}">
        <i data-lucide="mail-check" class="w-5 h-5"></i>
        <span class="text-[10px] font-medium">Status Izin</span>
    </a>

    <a href="{{ route('leave-request.index') }}"
       class="flex flex-col items-center gap-0.5 {{ request()->routeIs('leave-request.index') ? 'text-amber-600' : 'text-gray-400' }}">
        <i data-lucide="file-plus" class="w-5 h-5"></i>
        <span class="text-[10px] font-medium">Ajukan Izin</span>
    </a>

</nav>