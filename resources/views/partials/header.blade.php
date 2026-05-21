@php
    $user = auth()->user();
@endphp

<header
    class="sticky top-0 z-30 min-h-18 bg-white/95 border-b border-slate-200 px-4 py-3 backdrop-blur sm:px-5 lg:min-h-20 lg:px-6 lg:py-4 flex items-center justify-between gap-3">
    <div class="flex min-w-0 items-center gap-3">
        <button type="button"
            class="relative flex h-11 w-11 shrink-0 flex-col items-center justify-center gap-1.5 rounded-2xl border border-slate-200 bg-white shadow-sm lg:hidden"
            @click="openMobileMenu()" :aria-expanded="mobileMenuOpen.toString()" aria-controls="mobile-navigation-drawer"
            aria-label="Buka menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
        <div class="min-w-0">
            <h1 class="truncate text-xl font-extrabold tracking-tight font-['Plus_Jakarta_Sans'] sm:text-2xl">
                @yield('page_title', 'Dashboard')</h1>
            <p class="mt-1 line-clamp-1 text-sm text-slate-500">@yield('page_subtitle', 'Selamat datang kembali, ' . $user?->name)</p>
        </div>
    </div>
    <div class="hidden xl:flex items-center gap-3">

        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3.5 py-2">
            <div class="h-9 w-9 rounded-full bg-emerald-100 grid place-items-center font-bold text-emerald-700">
                {{ str($user?->name ?? 'FF')->substr(0, 2)->upper() }}</div>
            <div class="text-left">
                <div class="font-bold text-sm">{{ $user?->name }}</div>
                <div class="text-xs text-slate-500">{{ $user?->role?->role_name ?? 'Anggota' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    class="rounded-2xl border border-slate-200 bg-white p-2 text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                    aria-label="Keluar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
    <div class="hidden shrink-0 items-center gap-2 lg:flex xl:hidden">
        <a href="{{ route('transactions.create') }}"
            class="grid h-11 w-11 place-items-center rounded-2xl bg-emerald-600 text-xl font-extrabold text-white"
            aria-label="Tambah transaksi">+</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs font-extrabold text-slate-600">Keluar</button>
        </form>
    </div>
    <div class="flex shrink-0 items-center gap-2 lg:hidden">
        <a href="{{ route('transactions.create') }}"
            class="grid h-11 w-11 place-items-center rounded-2xl bg-emerald-600 text-xl font-extrabold text-white"
            aria-label="Tambah transaksi">+</a>
    </div>
</header>
