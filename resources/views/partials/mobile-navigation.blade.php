@php
    $user = auth()->user();
    $family = $user?->family;
    $memberCount = $family?->users()->count() ?? 0;
    $mobileMenus = [
        ['Dashboard','dashboard','dashboard','icon-wallet.svg'],
        ['Transaksi','transactions.index','transactions*','icon-income.svg'],
        ['Anggaran','budgets.index','budgets*','icon-budget.svg'],
        ['Dompet','wallets.index','wallets*','icon-shield.svg'],
        ['Menu',null,'','icon-family.svg'],
    ];
    $drawerMenus = [
        ['Dashboard','dashboard','dashboard','icon-wallet.svg'],
        ['Transaksi','transactions.index','transactions*','icon-income.svg'],
        ['Kategori','categories.index','categories*','icon-category-health.svg'],
        ['Anggaran','budgets.index','budgets*','icon-budget.svg'],
        ['Dompet','wallets.index','wallets*','icon-shield.svg'],
        ['Anggota Keluarga','family.members','family*','icon-family.svg'],
        ['Laporan','reports.history','reports*','icon-expense.svg'],
        ['Pengaturan','settings.index','settings*','icon-wifi.svg'],
    ];
@endphp

<div class="lg:hidden">
    <div x-show="mobileMenuOpen" x-cloak class="fixed inset-0 z-40 bg-slate-950/45 backdrop-blur-sm" @click="closeMobileMenu()"></div>

    <aside
        id="mobile-navigation-drawer"
        x-cloak
        role="dialog"
        aria-modal="true"
        aria-label="Navigasi mobile"
        class="mobile-drawer-panel fixed inset-x-3 bottom-3 z-50 max-h-[86dvh] overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-[0_24px_80px_rgba(15,23,42,0.22)] sm:inset-x-auto sm:right-5 sm:w-[420px]"
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-8 opacity-0 scale-[0.98]"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-8 opacity-0 scale-[0.98]"
    >
        <div class="flex items-center justify-between border-b border-slate-100 p-4">
            <div class="flex min-w-0 items-center gap-3">
                <img src="{{ asset('assets/svg/logo-famfinance.svg') }}" class="h-10 shrink-0" alt="FamFinance">
                <div class="min-w-0">
                    <div class="truncate text-sm font-extrabold text-slate-950">{{ $family?->family_name ?? 'FamFinance' }}</div>
                    <div class="text-xs font-semibold text-slate-500">{{ $memberCount }} anggota keluarga</div>
                </div>
            </div>
            <button type="button" class="grid h-10 w-10 place-items-center rounded-2xl bg-slate-100 text-slate-700" @click="closeMobileMenu()" aria-label="Tutup menu">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="max-h-[calc(86dvh-88px)] overflow-y-auto p-4">
            <div class="rounded-3xl bg-emerald-50 p-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/illustration/sidebar-family-illustration.png') }}" class="h-20 w-20 shrink-0 object-contain" alt="Keluarga">
                    <div class="min-w-0">
                        <div class="truncate font-extrabold text-slate-950">{{ $user?->name }}</div>
                        <div class="mt-1 text-sm font-semibold text-slate-500">{{ $user?->role?->role_name ?? 'Anggota' }}</div>
                    </div>
                </div>
            </div>

            <nav class="mt-4 grid grid-cols-2 gap-3">
                @foreach($drawerMenus as [$label,$route,$pattern,$icon])
                    <a href="{{ Route::has($route) ? route($route) : '#' }}" @click="closeMobileMenu()" class="flex min-h-20 min-w-0 items-center gap-3 rounded-2xl border border-slate-200 p-4 text-sm font-extrabold transition {{ request()->routeIs($pattern) ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-white text-slate-700' }}">
                        <img src="{{ asset('assets/svg/'.$icon) }}" class="h-6 w-6" alt="">
                        <span class="min-w-0 leading-tight">{{ $label }}</span>
                    </a>
                @endforeach
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button class="w-full rounded-3xl border border-rose-100 bg-rose-50 px-5 py-4 text-sm font-extrabold text-rose-700">Keluar dari Akun</button>
            </form>
        </div>
    </aside>

    <nav class="mobile-bottom-nav fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 px-3 pt-2 shadow-[0_-10px_35px_rgba(15,23,42,0.08)] backdrop-blur">
        <div class="mx-auto grid max-w-md grid-cols-5 gap-1 rounded-[24px]">
            @foreach($mobileMenus as [$label,$route,$pattern,$icon])
                @if($route)
                    <a href="{{ route($route) }}" class="flex min-h-[60px] flex-col items-center justify-center gap-1 rounded-2xl text-[11px] font-extrabold {{ request()->routeIs($pattern) ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500' }}">
                        <img src="{{ asset('assets/svg/'.$icon) }}" class="h-5 w-5" alt="">
                        <span>{{ $label }}</span>
                    </a>
                @else
                    <button type="button" @click="openMobileMenu()" :aria-expanded="mobileMenuOpen.toString()" aria-controls="mobile-navigation-drawer" class="flex min-h-[60px] flex-col items-center justify-center gap-1 rounded-2xl text-[11px] font-extrabold text-slate-500">
                        <img src="{{ asset('assets/svg/'.$icon) }}" class="h-5 w-5" alt="">
                        <span>{{ $label }}</span>
                    </button>
                @endif
            @endforeach
        </div>
    </nav>
</div>
