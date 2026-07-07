@extends('layouts.app')

@php
    $formatCurrency = fn($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    $formatPercent = fn($value) => ((float) $value >= 0 ? '↑ ' : '↓ ') .
        number_format(abs((float) $value), 1, ',', '.') .
        '%';
    $budgetPercentage = $budgetLimitTotal > 0 ? min(100, round(($budgetSpentTotal / $budgetLimitTotal) * 100)) : 0;
    $months = collect(range(1, 12))->mapWithKeys(
        fn($number) => [$number => \Carbon\Carbon::create($period->year, $number, 1)->translatedFormat('F')],
    );
    $years = range(now()->year - 2, now()->year + 2);
@endphp

@section('title', 'Dashboard - FamFinance')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan keuangan keluarga · Selamat datang kembali, ' . auth()->user()?->name . ' 👋')

@section('content')
    <div class="page-stack">
        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Total Saldo" :value="$formatCurrency($totalBalance)" icon="icon-wallet.svg" tone="emerald" :hint="$formatPercent($balanceChangePercentage) . ' dari estimasi awal bulan'" />
            <x-stat-card label="Pemasukan Bulan Ini" :value="$formatCurrency($incomeMonth)" icon="icon-income.svg" tone="emerald"
                :hint="$formatPercent($incomeChangePercentage) . ' dari bulan lalu'" />
            <x-stat-card label="Pengeluaran Bulan Ini" :value="$formatCurrency($expenseMonth)" icon="icon-expense.svg" tone="rose"
                :hint="$formatPercent($expenseChangePercentage) . ' dari bulan lalu'" />
            <x-stat-card label="Sisa Anggaran" :value="$formatCurrency($remainingBudget)" icon="icon-budget.svg" tone="amber" :hint="$budgetPercentage . '% dari total anggaran terpakai'" />
        </section>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
            <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-[130px_90px_auto] gap-2">
                <select name="month" class="form-control">
                    @foreach ($months as $number => $name)
                        <option value="{{ $number }}" @selected((int) $number === (int) $period->month)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="year" class="form-control">
                    @foreach ($years as $yearOption)
                        <option value="{{ $yearOption }}" @selected((int) $yearOption === (int) $period->year)>{{ $yearOption }}</option>
                    @endforeach
                </select>
                <button class="secondary-action">Terapkan</button>
            </form>
            <a href="{{ route('transactions.create') }}" class="primary-action">＋ Tambah Transaksi</a>
            <a href="{{ route('budgets.index') }}"
                class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-600 px-4 text-[13px] font-bold text-white">＋
                Buat Anggaran</a>
            <a href="{{ route('wallets.index') }}"
                class="inline-flex h-11 items-center justify-center rounded-xl bg-violet-600 px-4 text-[13px] font-bold text-white">＋
                Tambah Dompet</a>
        </div>

        <section class="grid min-w-0 gap-4 xl:grid-cols-12">
            <x-card class="p-4 xl:col-span-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-extrabold">Arus Kas</h2>
                        <div class="mt-2 flex gap-4 text-[10px] text-slate-500"><span class="flex items-center gap-2"><i
                                    class="h-2 w-2 rounded-full bg-emerald-500"></i>Pemasukan</span><span
                                class="flex items-center gap-2"><i
                                    class="h-2 w-2 rounded-full bg-rose-500"></i>Pengeluaran</span></div>
                    </div>
                    <span
                        class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-[10px] font-semibold text-slate-500">{{ $period->translatedFormat('F Y') }}</span>
                </div>
                <div class="mt-3 h-64"><canvas id="cashflowChart"></canvas></div>
            </x-card>

            <x-card class="p-4 xl:col-span-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-extrabold">Anggaran per Kategori</h2><a href="{{ route('budgets.index') }}"
                        class="text-[10px] font-bold text-emerald-600">Lihat semua</a>
                </div>
                <div class="mt-4 grid gap-3">
                    @forelse($budgets->take(5) as $budget)
                        @php($tone = $budget['percentage'] >= 100 ? 'rose' : ($budget['percentage'] >= 75 ? 'amber' : 'emerald'))
                        <x-progress-row :label="$budget['category']?->category_name ?? 'Lainnya'" :value="$budget['percentage']" :meta="$formatCurrency($budget['spent']) . ' / ' . $formatCurrency($budget['limit'])" :tone="$tone" />
                    @empty
                        <div class="ff-empty py-8">Belum ada anggaran periode ini.</div>
                    @endforelse
                </div>
            </x-card>

            <x-card class="p-4 xl:col-span-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-extrabold">Dompet</h2><a href="{{ route('wallets.index') }}"
                        class="text-[10px] font-bold text-emerald-600">Kelola</a>
                </div>
                <div class="mt-3 grid gap-2">
                    @forelse($wallets->take(4) as $wallet)
                        <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 p-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span
                                    class="grid h-9 w-9 shrink-0 place-items-center rounded-lg {{ $wallet->type === 'bank' ? 'bg-blue-600' : ($wallet->type === 'e-wallet' ? 'bg-violet-600' : 'bg-emerald-500') }} text-xs font-extrabold text-white">{{ $wallet->type === 'cash' ? 'C' : str($wallet->wallet_name)->substr(0, 2)->upper() }}</span>
                                <div class="min-w-0"><b
                                        class="block truncate text-[11px]">{{ $wallet->wallet_name }}</b><span
                                        class="mt-1 block text-[9px] text-slate-500">{{ ucfirst($wallet->type) }}</span>
                                </div>
                            </div>
                            <b class="whitespace-nowrap text-[11px]">{{ $formatCurrency($wallet->balance) }}</b>
                        </div>
                    @empty
                        <div class="ff-empty py-8">Belum ada dompet.</div>
                    @endforelse
                </div>
                <div class="mt-3 flex items-center justify-between rounded-xl bg-emerald-50 px-3 py-2 text-[10px]">
                    <span>Total Saldo</span><b class="text-emerald-700">{{ $formatCurrency($totalBalance) }}</b>
                </div>
            </x-card>
        </section>

        <section class="grid min-w-0 gap-4 xl:grid-cols-12">
            <x-card class="overflow-hidden xl:col-span-5">
                <div class="ff-card-header">
                    <h2 class="text-sm font-extrabold">Transaksi Terbaru</h2><a href="{{ route('transactions.index') }}"
                        class="text-[10px] font-bold text-emerald-600">Lihat semua</a>
                </div>
                <div class="ff-table-wrap">
                    <table class="ff-table min-w-[580px]">
                        <thead>
                            <tr>
                                <th>Deskripsi</th>
                                <th>Kategori</th>
                                <th class="text-right">Jumlah</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td><a href="{{ route('transactions.edit', $transaction) }}"
                                            class="font-bold text-slate-950">{{ $transaction->title }}</a></td>
                                    <td>{{ $transaction->category?->category_name ?? '-' }}</td>
                                    <td
                                        class="text-right font-bold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $transaction->type === 'income' ? '' : '-' }}{{ $formatCurrency($transaction->amount) }}
                                    </td>
                                    <td class="whitespace-nowrap">
                                        {{ $transaction->transaction_date?->translatedFormat('d M Y') }}</td>
                                    <td><x-badge
                                            tone="{{ $transaction->status === 'success' ? 'success' : 'cancel' }}">{{ $transaction->status === 'success' ? 'Sukses' : 'Batal' }}</x-badge>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="ff-empty">Belum ada transaksi.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('transactions.index') }}"
                    class="block border-t border-slate-100 py-3 text-center text-[10px] font-bold text-emerald-600">Lihat
                    semua transaksi →</a>
            </x-card>

            <x-card class="p-4 xl:col-span-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-extrabold">Pengeluaran per Kategori</h2><span
                        class="rounded-lg border border-slate-200 px-2 py-1 text-[9px]">{{ $period->translatedFormat('F') }}</span>
                </div>
                <div class="mt-3 h-64"><canvas id="donutChart"></canvas></div>
                <a href="{{ route('reports.history') }}"
                    class="mt-2 block text-center text-[10px] font-bold text-emerald-600">Lihat laporan lengkap →</a>
            </x-card>

            <x-card class="p-4 xl:col-span-3">
                <h2 class="text-sm font-extrabold">Aktivitas Terbaru</h2>
                <div class="mt-4 grid gap-4">
                    @forelse($histories->take(5) as $history)
                        <div class="flex gap-3">
                            <span
                                class="grid h-9 w-9 shrink-0 place-items-center rounded-full {{ $loop->even ? 'bg-blue-50' : 'bg-emerald-50' }}"><img
                                    src="{{ asset('assets/svg/' . ($loop->even ? 'icon-family.svg' : 'icon-wallet.svg')) }}"
                                    class="h-5 w-5" alt=""></span>
                            <div class="min-w-0"><b
                                    class="line-clamp-1 text-[10px]">{{ $history->note ?? ($history->transaction?->title ?? 'Aktivitas transaksi') }}</b>
                                <p class="mt-1 line-clamp-2 text-[9px] leading-4 text-slate-500">
                                    {{ $history->user?->name }} · {{ $history->created_at?->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="ff-empty py-8">Belum ada aktivitas.</div>
                    @endforelse
                </div>
            </x-card>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.Chart) return;
            const cash = document.getElementById('cashflowChart');
            if (cash) new window.Chart(cash, {
                type: 'line',
                data: {
                    labels: @json($cashflow['labels']),
                    datasets: [{
                            label: 'Pemasukan',
                            data: @json($cashflow['income']),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16,185,129,.10)',
                            fill: true,
                            tension: .35,
                            pointRadius: 0,
                            borderWidth: 2
                        },
                        {
                            label: 'Pengeluaran',
                            data: @json($cashflow['expense']),
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239,68,68,.08)',
                            fill: true,
                            tension: .35,
                            pointRadius: 0,
                            borderWidth: 2
                        },
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => Number(v / 1000000).toLocaleString('id-ID') + 'jt',
                                font: {
                                    size: 9
                                }
                            },
                            grid: {
                                color: '#e2e8f0'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxTicksLimit: 7,
                                font: {
                                    size: 9
                                }
                            }
                        }
                    }
                },
            });
            const donut = document.getElementById('donutChart');
            if (donut) new window.Chart(donut, {
                type: 'doughnut',
                data: {
                    labels: @json($expenseBreakdown->keys()->values()),
                    datasets: [{
                        data: @json($expenseBreakdown->values()->values()),
                        backgroundColor: ['#fb7185', '#8b5cf6', '#f59e0b', '#3b82f6', '#10b981',
                            '#94a3b8'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 9,
                                usePointStyle: true,
                                font: {
                                    size: 9
                                },
                                padding: 12
                            }
                        }
                    }
                },
            });
        });
    </script>
@endpush
