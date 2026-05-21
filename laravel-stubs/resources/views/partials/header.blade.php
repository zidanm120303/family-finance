<header class="h-24 bg-white border-b border-slate-200 px-6 lg:px-8 flex items-center justify-between gap-6">
    <div>
        <h1 class="text-3xl font-extrabold tracking-tight font-['Plus_Jakarta_Sans']">@yield('page_title', 'Dashboard')</h1>
        <p class="text-slate-500 mt-1">@yield('page_subtitle', 'Selamat datang kembali, Budi Pratama 👋')</p>
    </div>
    <div class="hidden md:flex items-center gap-4">
        <div class="relative">
            <input class="w-[360px] rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 outline-none" placeholder="Cari transaksi, kategori, atau anggota...">
            <span class="absolute left-4 top-3.5 text-slate-400">⌕</span>
        </div>
        <button class="relative h-12 w-12 rounded-full border border-slate-200 bg-white">🔔<span class="absolute -top-1 -right-1 grid h-5 w-5 place-items-center rounded-full bg-rose-500 text-[10px] text-white font-bold">3</span></button>
        <button class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-2">
            <div class="h-10 w-10 rounded-full bg-emerald-100 grid place-items-center font-bold text-emerald-700">BP</div>
            <div class="text-left"><div class="font-bold text-sm">Budi Pratama</div><div class="text-xs text-slate-500">Kepala Keluarga</div></div>
            <span>⌄</span>
        </button>
    </div>
</header>
