@php
    $family = auth()->user()?->family;
    $memberCount = $family?->users()->count() ?? 0;
    $menus = [
        ['label' => 'Dashboard', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => 'icon-wallet.svg'],
        ['label' => 'Transaksi', 'url' => route('transactions.index'), 'active' => request()->routeIs('transactions*'), 'icon' => 'icon-income.svg'],
        ['label' => 'Kategori', 'url' => route('categories.index'), 'active' => request()->routeIs('categories*'), 'icon' => 'icon-category-health.svg'],
        ['label' => 'Anggaran', 'url' => route('budgets.index'), 'active' => request()->routeIs('budgets*'), 'icon' => 'icon-budget.svg'],
        ['label' => 'Dompet', 'url' => route('wallets.index'), 'active' => request()->routeIs('wallets*'), 'icon' => 'icon-shield.svg'],
        ['label' => 'Anggota Keluarga', 'url' => route('family.members'), 'active' => request()->routeIs('family*'), 'icon' => 'icon-family.svg'],
        ['label' => 'Riwayat', 'url' => route('reports.history', ['tab' => 'history']), 'active' => request()->routeIs('reports*') && request('tab', 'report') === 'history', 'icon' => 'icon-wallet.svg'],
        ['label' => 'Laporan', 'url' => route('reports.history', ['tab' => 'report']), 'active' => request()->routeIs('reports*') && request('tab', 'report') !== 'history', 'icon' => 'icon-expense.svg'],
        ['label' => 'Pengaturan', 'url' => route('settings.index'), 'active' => request()->routeIs('settings*'), 'icon' => 'icon-wifi.svg'],
    ];
@endphp

<aside class="sticky top-0 hidden h-screen w-[248px] shrink-0 flex-col overflow-y-auto border-r border-slate-200 bg-white px-3 py-5 lg:flex">
    <a href="{{ route('dashboard') }}" class="mb-5 flex h-12 items-center px-1">
        <img src="{{ asset('assets/svg/logo-famfinance.svg') }}" alt="FamFinance" class="h-10 w-auto">
    </a>

    <nav class="grid gap-1">
        @foreach ($menus as $menu)
            <a href="{{ $menu['url'] }}"
                class="flex min-h-11 items-center gap-3 rounded-xl px-3 text-[13px] font-semibold transition {{ $menu['active'] ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-lg {{ $menu['active'] ? 'bg-emerald-100/80' : '' }}">
                    <img src="{{ asset('assets/svg/' . $menu['icon']) }}" class="h-5 w-5" alt="">
                </span>
                <span class="min-w-0">{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="mt-auto grid gap-3 pt-5">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
            <div class="h-24 bg-gradient-to-br from-emerald-50 to-amber-50 px-3 pt-2">
                <img src="{{ asset('assets/illustration/sidebar-family-illustration.png') }}" alt="Keluarga"
                    class="mx-auto h-[88px] w-full object-contain object-bottom">
            </div>
            <div class="flex items-center justify-between gap-2 px-4 py-3">
                <div class="min-w-0">
                    <div class="truncate text-xs font-extrabold text-slate-950">{{ $family?->family_name ?? 'Keluarga' }}</div>
                    <div class="mt-1 text-[10px] font-medium text-slate-500">{{ $memberCount }} anggota keluarga</div>
                </div>
                <svg class="h-4 w-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m8 10 4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-4 text-[10px] leading-5 text-slate-600">
            <div class="flex items-start gap-2 font-bold text-slate-800">
                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-lg bg-emerald-600">
                    <img src="{{ asset('assets/svg/icon-shield.svg') }}" class="h-4 w-4 brightness-0 invert" alt="">
                </span>
                <span>Keuangan sehat dimulai dari kebiasaan baik.</span>
            </div>
            <p class="mt-2 pl-9">Ayo terus atur keuangan keluarga dengan bijak.</p>
        </div>
    </div>
</aside>
