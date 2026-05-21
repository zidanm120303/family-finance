<aside class="w-[280px] bg-white border-r border-slate-200 min-h-screen p-5 hidden lg:flex flex-col overflow-y-auto">
    <a href="{{ route('dashboard') }}" class="mb-8 block">
        <img src="{{ asset('assets/svg/logo-famfinance.svg') }}" alt="FamFinance" class="h-14 w-auto">
    </a>
    @php
        $menus = [
            ['Dashboard', 'dashboard', 'dashboard', '▦'],
            ['Transaksi', 'transactions.index', 'transactions*', '⇄'],
            ['Kategori', 'categories.index', 'categories*', '□'],
            ['Anggaran', 'budgets.index', 'budgets*', '▣'],
            ['Dompet', 'wallets.index', 'wallets*', '▤'],
            ['Anggota Keluarga', 'family.members', 'family*', '👥'],
            ['Laporan', 'reports.history', 'reports*', '▥'],
        ];
    @endphp
    <nav class="space-y-2.5">
        @foreach ($menus as [$label, $route, $pattern, $icon])
            <a href="{{ Route::has($route) ? route($route) : '#' }}"
                class="flex items-center gap-4 rounded-2xl px-5 py-3.5 text-base font-bold leading-tight transition {{ request()->routeIs($pattern) ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="w-8 shrink-0 text-center text-xl leading-none">{{ $icon }}</span><span class="min-w-0">{{ $label }}</span>
            </a>
        @endforeach
    </nav>
    <div class="mt-auto space-y-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <img src="{{ asset('assets/svg/icon-family.svg') }}" alt="Keluarga" class="mx-auto h-24">
            <div class="font-bold text-slate-950">Keluarga Pratama</div>
            <div class="text-sm text-slate-500">4 anggota keluarga</div>
        </div>
        <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-5 text-sm text-slate-600">
            <div class="font-bold text-slate-800 mb-2">Keuangan sehat dimulai dari kebiasaan baik.</div>
            <p>Ayo terus atur keuangan keluarga dengan bijak! 💚</p>
        </div>
    </div>
</aside>
