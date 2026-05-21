@php
    $family = auth()->user()?->family;
    $memberCount = $family?->users()->count() ?? 0;
@endphp

<aside class="w-[17rem] bg-white border-r border-slate-200 min-h-screen p-4 hidden lg:flex flex-col overflow-y-auto">
    <a href="{{ route('dashboard') }}" class="mb-6 block">
        <img src="{{ asset('assets/svg/logo-famfinance.svg') }}" alt="FamFinance" class="h-12 w-auto">
    </a>
    @php
        $menus = [
            ['Dashboard', 'dashboard', 'dashboard', 'icon-wallet.svg'],
            ['Transaksi', 'transactions.index', 'transactions*', 'icon-income.svg'],
            ['Kategori', 'categories.index', 'categories*', 'icon-category-health.svg'],
            ['Anggaran', 'budgets.index', 'budgets*', 'icon-budget.svg'],
            ['Dompet', 'wallets.index', 'wallets*', 'icon-shield.svg'],
            ['Anggota Keluarga', 'family.members', 'family*', 'icon-family.svg'],
            ['Laporan', 'reports.history', 'reports*', 'icon-expense.svg'],
            ['Pengaturan', 'settings.index', 'settings*', 'icon-wifi.svg'],
        ];
    @endphp
    <nav class="space-y-2">
        @foreach ($menus as [$label, $route, $pattern, $icon])
            <a href="{{ Route::has($route) ? route($route) : '#' }}"
                class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold leading-tight transition {{ request()->routeIs($pattern) ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">
                <img src="{{ asset('assets/svg/' . $icon) }}" class="h-7 w-7 shrink-0" alt=""><span
                    class="min-w-0">{{ $label }}</span>
            </a>
        @endforeach
    </nav>
    <div class="mt-auto space-y-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-3 shadow-sm text-center">
            <img src="{{ asset('assets/illustration/sidebar-family-illustration.png') }}" alt="Keluarga"
                class="mx-auto h-20 object-contain">
            <div class="text-sm font-bold text-slate-950">{{ $family?->family_name ?? 'Keluarga' }}</div>
            <div class="text-xs text-slate-500">{{ $memberCount }} anggota keluarga</div>
        </div>
        <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-4 text-xs leading-5 text-slate-600">
            <div class="font-bold text-slate-800 mb-2">Keuangan sehat dimulai dari kebiasaan baik.</div>
            <p>Ayo terus atur keuangan keluarga dengan bijak.</p>
        </div>
    </div>
</aside>
