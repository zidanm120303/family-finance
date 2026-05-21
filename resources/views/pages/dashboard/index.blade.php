@extends('layouts.app')
@section('title', 'Dashboard - FamFinance')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Selamat datang kembali, ' . (auth()->user()?->name ?? 'Keluarga'))
@section('content')
    @php
        $formatCurrency = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
        $budgetUsagePercent = $budgetLimitTotal > 0 ? round(($budgetSpentTotal / $budgetLimitTotal) * 100) : 0;
        $walletTone = [
            'cash' => 'cash',
            'bank' => 'bank',
            'e-wallet' => 'wallet',
        ];
        $expensePalette = ['#FF6B57', '#7C5CE6', '#F59E0B', '#2D9CDB', '#6D5BD0', '#94A3B8'];
        $expenseTotal = (float) $expenseBreakdown->sum();
    @endphp

    <div class="dashboard-page">
        <div class="dashboard-stat-grid">
            <div class="dashboard-stat-card">
                <span class="dashboard-stat-icon dashboard-stat-emerald">
                    <img src="{{ asset('assets/svg/icon-wallet.svg') }}" alt="">
                </span>
                <div class="min-w-0">
                    <div class="dashboard-stat-label">Total Saldo</div>
                    <div class="dashboard-stat-value">{{ $formatCurrency($totalBalance) }}</div>
                    <div class="dashboard-stat-meta {{ $balanceChangePercentage >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $balanceChangePercentage >= 0 ? 'Naik' : 'Turun' }}
                        {{ abs($balanceChangePercentage) }}% dari estimasi bulan lalu
                    </div>
                </div>
                <span class="dashboard-info-dot">i</span>
            </div>

            <div class="dashboard-stat-card">
                <span class="dashboard-stat-icon dashboard-stat-emerald">
                    <img src="{{ asset('assets/svg/icon-income.svg') }}" alt="">
                </span>
                <div class="min-w-0">
                    <div class="dashboard-stat-label">Pemasukan Bulan Ini</div>
                    <div class="dashboard-stat-value">{{ $formatCurrency($incomeMonth) }}</div>
                    <div class="dashboard-stat-meta {{ $incomeChangePercentage >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $incomeChangePercentage >= 0 ? 'Naik' : 'Turun' }}
                        {{ abs($incomeChangePercentage) }}% dari bulan lalu
                    </div>
                </div>
                <span class="dashboard-info-dot">i</span>
            </div>

            <div class="dashboard-stat-card">
                <span class="dashboard-stat-icon dashboard-stat-rose">
                    <img src="{{ asset('assets/svg/icon-expense.svg') }}" alt="">
                </span>
                <div class="min-w-0">
                    <div class="dashboard-stat-label">Pengeluaran Bulan Ini</div>
                    <div class="dashboard-stat-value">{{ $formatCurrency($expenseMonth) }}</div>
                    <div class="dashboard-stat-meta {{ $expenseChangePercentage <= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $expenseChangePercentage >= 0 ? 'Naik' : 'Turun' }}
                        {{ abs($expenseChangePercentage) }}% dari bulan lalu
                    </div>
                </div>
                <span class="dashboard-info-dot">i</span>
            </div>

            <div class="dashboard-stat-card">
                <span class="dashboard-stat-icon dashboard-stat-amber">
                    <img src="{{ asset('assets/svg/icon-budget.svg') }}" alt="">
                </span>
                <div class="min-w-0">
                    <div class="dashboard-stat-label">Sisa Anggaran</div>
                    <div class="dashboard-stat-value">{{ $formatCurrency($remainingBudget) }}</div>
                    <div class="dashboard-stat-meta text-amber-600">{{ $budgetUsagePercent }}% dari total anggaran</div>
                </div>
                <span class="dashboard-info-dot">i</span>
            </div>
        </div>

        <div class="dashboard-actions">
            <a href="{{ route('transactions.create') }}" class="dashboard-action-btn dashboard-action-emerald">
                <span>+</span> Tambah Transaksi
            </a>
            <a href="{{ route('budgets.index') }}" class="dashboard-action-btn dashboard-action-blue">
                <span>+</span> Buat Anggaran
            </a>
            <a href="{{ route('wallets.index') }}" class="dashboard-action-btn dashboard-action-purple">
                <span>+</span> Tambah Dompet
            </a>
        </div>

        <div class="dashboard-main-grid">
            <x-card class="dashboard-card dashboard-chart-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2>Arus Kas</h2>
                        <div class="dashboard-chart-legend">
                            <span><i class="bg-emerald-500"></i>Pemasukan</span>
                            <span><i class="bg-rose-500"></i>Pengeluaran</span>
                        </div>
                    </div>
                    <span class="dashboard-period-pill">{{ $period->translatedFormat('F Y') }}</span>
                </div>
                <div class="dashboard-chart-wrap">
                    <canvas id="cashflowChart"></canvas>
                </div>
            </x-card>

            <x-card class="dashboard-card dashboard-budget-card">
                <div class="dashboard-card-header">
                    <h2>Anggaran per Kategori</h2>
                    <a href="{{ route('budgets.index') }}">Lihat semua</a>
                </div>
                <div class="dashboard-budget-list">
                    @forelse($budgets->take(5) as $budget)
                        @php
                            $budgetColor = $budget['category']?->color ?: '#10B981';
                            $budgetIcon = $budget['category']?->icon ?: 'icon-budget.svg';
                        @endphp
                        <div class="dashboard-budget-row">
                            <span class="dashboard-budget-icon" style="--budget-color: {{ $budgetColor }};">
                                <img src="{{ asset('assets/svg/' . $budgetIcon) }}" alt="">
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="dashboard-budget-title">
                                    <span>{{ $budget['category']?->category_name ?? 'Lainnya' }}</span>
                                    <strong>{{ min(100, $budget['percentage']) }}%</strong>
                                </div>
                                <div class="dashboard-budget-track">
                                    <span style="width: {{ min(100, $budget['percentage']) }}%; background: {{ $budgetColor }};"></span>
                                </div>
                                <div class="dashboard-budget-meta">
                                    {{ $formatCurrency($budget['spent']) }} / {{ $formatCurrency($budget['limit']) }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="dashboard-empty">Belum ada anggaran untuk bulan ini.</p>
                    @endforelse
                </div>
            </x-card>

            <x-card class="dashboard-card dashboard-wallet-card">
                <div class="dashboard-card-header">
                    <h2>Dompet</h2>
                    <a href="{{ route('wallets.index') }}">Kelola</a>
                </div>
                <div class="dashboard-wallet-list">
                    @forelse($wallets->take(4) as $wallet)
                        @php
                            $tone = $walletTone[$wallet->type] ?? 'wallet';
                            $walletLabel = strtoupper(substr($wallet->wallet_name, 0, 4));
                        @endphp
                        <a href="{{ route('wallets.index') }}" class="dashboard-wallet-row">
                            <span class="dashboard-wallet-logo dashboard-wallet-{{ $tone }}">
                                {{ $wallet->type === 'cash' ? 'Rp' : $walletLabel }}
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="dashboard-wallet-name">{{ $wallet->wallet_name }}</span>
                                <strong>{{ $formatCurrency($wallet->balance) }}</strong>
                            </span>
                            <span class="dashboard-row-arrow">&rsaquo;</span>
                        </a>
                    @empty
                        <p class="dashboard-empty">Belum ada dompet.</p>
                    @endforelse
                </div>
                <div class="dashboard-wallet-total">
                    <span>Total Saldo</span>
                    <strong>{{ $formatCurrency($totalBalance) }}</strong>
                </div>
            </x-card>

            <x-card class="dashboard-card dashboard-transactions-card">
                <div class="dashboard-card-header">
                    <h2>Transaksi Terbaru</h2>
                    <a href="{{ route('transactions.index') }}">Lihat semua</a>
                </div>
                <div class="dashboard-mini-table-wrap">
                    <table class="dashboard-mini-table">
                        <thead>
                            <tr>
                                <th>Deskripsi</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Tanggal</th>
                                <th>Metode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr>
                                    <td>
                                        <div class="dashboard-transaction-title">
                                            <span class="dashboard-transaction-icon {{ $trx->type === 'income' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                @if ($trx->type === 'income')
                                                    &darr;
                                                @else
                                                    &nearr;
                                                @endif
                                            </span>
                                            <span>{{ $trx->title }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="dashboard-tag {{ $trx->type === 'income' ? 'dashboard-tag-green' : 'dashboard-tag-amber' }}">
                                            {{ $trx->category?->category_name ?? 'Lainnya' }}
                                        </span>
                                    </td>
                                    <td class="{{ $trx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $trx->type === 'expense' ? '- ' : '' }}{{ $formatCurrency($trx->amount) }}
                                    </td>
                                    <td>{{ $trx->transaction_date->translatedFormat('d M Y') }}</td>
                                    <td>{{ $trx->wallet?->wallet_name ?? ucfirst($trx->payment_method) }}</td>
                                    <td><span class="dashboard-status">Selesai</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="dashboard-empty">Belum ada transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('transactions.index') }}" class="dashboard-card-link">Lihat semua transaksi &rarr;</a>
            </x-card>

            <x-card class="dashboard-card dashboard-expense-card">
                <div class="dashboard-card-header">
                    <h2>Pengeluaran per Kategori</h2>
                    <span class="dashboard-period-pill">{{ $period->translatedFormat('F Y') }}</span>
                </div>
                <div class="dashboard-donut-layout">
                    <div class="dashboard-donut-wrap">
                        <canvas id="donutChart"></canvas>
                    </div>
                    <div class="dashboard-expense-list">
                        @forelse($expenseBreakdown->take(6) as $label => $amount)
                            @php
                                $color = $expensePalette[$loop->index % count($expensePalette)];
                                $percentage = $expenseTotal > 0 ? round(((float) $amount / $expenseTotal) * 100, 1) : 0;
                            @endphp
                            <div class="dashboard-expense-row">
                                <span style="background: {{ $color }}"></span>
                                <div>
                                    <strong>{{ $label }}</strong>
                                    <p>{{ $formatCurrency($amount) }} <em>({{ $percentage }}%)</em></p>
                                </div>
                            </div>
                        @empty
                            <p class="dashboard-empty">Belum ada pengeluaran.</p>
                        @endforelse
                    </div>
                </div>
                <a href="{{ route('reports.history') }}" class="dashboard-card-link">Lihat laporan lengkap &rarr;</a>
            </x-card>

            <x-card class="dashboard-card dashboard-activity-card">
                <div class="dashboard-card-header">
                    <h2>Aktivitas Terbaru</h2>
                </div>
                <div class="dashboard-activity-list">
                    @forelse($histories as $history)
                        <div class="dashboard-activity-row">
                            <span class="dashboard-activity-icon dashboard-activity-{{ $history->action }}">
                                @if ($history->action === 'delete')
                                    !
                                @elseif ($history->action === 'update')
                                    &#8635;
                                @else
                                    +
                                @endif
                            </span>
                            <div>
                                <strong>{{ ucfirst($history->action) }} {{ $history->transaction?->title ?? 'transaksi' }}</strong>
                                <p>{{ $history->user?->name ?? 'Sistem' }} &middot; {{ $history->created_at?->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="dashboard-empty">Belum ada aktivitas.</p>
                    @endforelse
                </div>
                <a href="{{ route('reports.history') }}" class="dashboard-card-link">Lihat semua aktivitas &rarr;</a>
            </x-card>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cashflow = @json($cashflow);
            const cashflowMonth = @json($period->translatedFormat('M'));
            const cashflowEl = document.getElementById('cashflowChart');

            if (cashflowEl) {
                new window.Chart(cashflowEl, {
                    type: 'line',
                    data: {
                        labels: cashflow.labels.map((day) => `${day} ${cashflowMonth}`),
                        datasets: [{
                                label: 'Pemasukan',
                                data: cashflow.income,
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.14)',
                                borderWidth: 3,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                tension: 0.38,
                                fill: true,
                            },
                            {
                                label: 'Pengeluaran',
                                data: cashflow.expense,
                                borderColor: '#EF4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.10)',
                                borderWidth: 3,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                tension: 0.38,
                                fill: true,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `${context.dataset.label}: Rp ${Number(context.raw).toLocaleString('id-ID')}`,
                                },
                            },
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxTicksLimit: 7
                                },
                            },
                            y: {
                                grid: {
                                    color: '#E2E8F0',
                                    borderDash: [5, 5],
                                },
                                ticks: {
                                    callback: (value) => value >= 1000000 ? `${value / 1000000}jt` : value,
                                },
                            },
                        },
                    },
                });
            }

            const donutLabels = @json($expenseBreakdown->keys()->take(6)->values());
            const donutData = @json($expenseBreakdown->values()->take(6)->values());
            const donutEl = document.getElementById('donutChart');

            if (donutEl) {
                new window.Chart(donutEl, {
                    type: 'doughnut',
                    data: {
                        labels: donutLabels.length ? donutLabels : ['Belum ada data'],
                        datasets: [{
                            data: donutData.length ? donutData : [1],
                            backgroundColor: @json($expensePalette),
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        cutout: '62%',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                        },
                    },
                });
            }
        });
    </script>
@endsection
