@php
    $user = auth()->user();
@endphp

<header class="sticky top-0 z-30 flex min-h-[88px] items-center justify-between gap-4 border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur sm:px-5 lg:px-7">
    <div class="flex min-w-0 items-center gap-3">
        <button type="button"
            class="ff-icon-button lg:hidden"
            @click="openMobileMenu()" :aria-expanded="mobileMenuOpen.toString()" aria-controls="mobile-navigation-drawer"
            aria-label="Buka menu">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <div class="min-w-0">
            <h1 class="ff-page-title truncate">@yield('page_title', 'Dashboard')</h1>
            <div class="mt-1 flex min-w-0 items-center gap-2 text-[11px] font-medium text-slate-500">
                <a href="{{ route('dashboard') }}" class="hidden hover:text-emerald-700 sm:inline">Dashboard</a>
                <svg class="hidden h-3 w-3 sm:block" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span class="truncate">@yield('page_subtitle', 'Selamat datang kembali, ' . $user?->name)</span>
            </div>
        </div>
    </div>

    <div class="flex shrink-0 items-center gap-2 sm:gap-3">
        <form method="GET" action="{{ route('transactions.index') }}" class="relative hidden xl:block">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                <path d="m20 20-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <input name="search" value="{{ request()->routeIs('transactions.index') ? request('search') : '' }}"
                class="h-11 w-[310px] rounded-xl border border-slate-200 bg-white pl-10 pr-4 text-xs font-medium text-slate-700 outline-none placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                placeholder="Cari transaksi, kategori, atau anggota...">
        </form>

        <button type="button" class="ff-icon-button relative hidden sm:grid" aria-label="Notifikasi">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-rose-500 px-1 text-[9px] font-extrabold text-white">3</span>
        </button>

        <div class="hidden items-center gap-3 rounded-xl border border-slate-200 bg-white p-1.5 pr-3 md:flex">
            <div class="grid h-9 w-9 place-items-center rounded-full bg-emerald-100 text-xs font-extrabold text-emerald-700">
                {{ str($user?->name ?? 'FF')->substr(0, 2)->upper() }}
            </div>
            <div class="min-w-0 xl:min-w-28">
                <div class="max-w-32 truncate text-xs font-extrabold text-slate-950">{{ $user?->name }}</div>
                <div class="mt-0.5 max-w-32 truncate text-[10px] font-medium text-slate-500">{{ $user?->role?->role_name ?? 'Anggota' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="grid h-8 w-8 place-items-center rounded-lg text-slate-500 transition hover:bg-slate-50 hover:text-rose-600" aria-label="Keluar">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M14 8V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2v-3M10 12h11m0 0-3-3m3 3-3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>
        </div>

        <a href="{{ route('transactions.create') }}" class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-600 text-white md:hidden" aria-label="Tambah transaksi">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
            </svg>
        </a>
    </div>
</header>
