@extends('layouts.app')
@section('page_title', 'Laporan & Riwayat')
@section('page_subtitle', 'Pantau kinerja keuangan keluarga dan riwayat perubahan data')
@section('content')
    @php
        $formatCurrency = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
        $expensePalette = ['#FF6B57', '#7C5CE6', '#2D9CDB', '#14B8A6', '#2F80ED', '#94A3B8'];
        $expenseTotal = (float) $expenseByCategory->sum();
        $historyTone = [
            'create' => 'create',
            'update' => 'update',
            'delete' => 'delete',
        ];
    @endphp

    <div class="report-page">
        <div class="report-tabs">
            <a href="{{ route('reports.history') }}" class="is-active">Laporan</a>
            <a href="#audit-log">Riwayat Perubahan</a>
        </div>

        <section class="report-filter-row">
            <form method="GET" action="{{ route('reports.history') }}" class="report-filter-form">
                <label>
                    <span>Periode</span>
                    <input name="period" type="month" value="{{ $period->format('Y-m') }}">
                </label>
                <label>
                    <span>Kategori</span>
                    <select name="category_id">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) request('category_id') === $category->id)>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" class="report-soft-button">Filter</button>
            </form>
            <div class="report-export-actions">
                <a href="{{ route('reports.export-pdf') }}" class="report-export-pdf">
                    <span>PDF</span>
                    Unduh PDF
                </a>
                <a href="{{ route('reports.export-excel') }}" class="report-export-excel">
                    <span>XLS</span>
                    Export Excel
                </a>
            </div>
        </section>

        <section class="report-layout-grid">
            <div class="report-analytics-stack">
                <x-card class="report-chart-card">
                    <div class="report-card-header">
                        <h2>Pemasukan vs Pengeluaran</h2>
                        <span>i</span>
                    </div>
                    <div class="report-chart-legend">
                        <span><i class="bg-emerald-500"></i>Pemasukan</span>
                        <span><i class="bg-rose-500"></i>Pengeluaran</span>
                    </div>
                    <div class="report-chart-wrap report-bar-wrap">
                        <canvas id="reportIncomeExpenseChart"></canvas>
                    </div>
                </x-card>

                <x-card class="report-donut-card">
                    <div class="report-card-header">
                        <h2>Pengeluaran per Kategori</h2>
                        <span>i</span>
                    </div>
                    <div class="report-donut-layout">
                        <div class="report-donut-wrap">
                            <canvas id="reportExpenseDonut"></canvas>
                        </div>
                        <div class="report-expense-list">
                            @forelse ($expenseByCategory->take(6) as $label => $amount)
                                @php
                                    $color = $expensePalette[$loop->index % count($expensePalette)];
                                    $percentage = $expenseTotal > 0 ? round(((float) $amount / $expenseTotal) * 100, 1) : 0;
                                @endphp
                                <div>
                                    <span style="background: {{ $color }}"></span>
                                    <p><strong>{{ $label }}</strong>{{ $formatCurrency($amount) }} ({{ $percentage }}%)</p>
                                </div>
                            @empty
                                <p class="report-empty">Belum ada pengeluaran.</p>
                            @endforelse
                        </div>
                    </div>
                </x-card>

                <div class="report-bottom-grid">
                    <x-card class="report-cashflow-card">
                        <div class="report-card-header">
                            <h2>Arus Kas Bulanan (Cashflow)</h2>
                        </div>
                        <div class="report-chart-legend">
                            <span><i class="bg-emerald-500"></i>Pemasukan</span>
                            <span><i class="bg-rose-500"></i>Pengeluaran</span>
                            <span><i class="bg-blue-500"></i>Saldo Bersih</span>
                        </div>
                        <div class="report-chart-wrap report-line-wrap">
                            <canvas id="reportCashflowChart"></canvas>
                        </div>
                    </x-card>

                    <x-card class="report-download-card">
                        <h2>Laporan Bulanan</h2>
                        <div class="report-file-preview">
                            <img src="{{ asset('assets/svg/logo-famfinance.svg') }}" alt="FamFinance">
                            <strong>{{ $period->translatedFormat('F Y') }}</strong>
                            <p>Total pemasukan {{ $formatCurrency($incomeTotal) }}</p>
                            <p>Total pengeluaran {{ $formatCurrency($expenseTotal) }}</p>
                        </div>
                        <p>Unduh laporan lengkap bulan {{ $period->translatedFormat('F Y') }} dalam format PDF.</p>
                        <a href="{{ route('reports.export-pdf') }}">Unduh Laporan {{ $period->translatedFormat('M Y') }}</a>
                    </x-card>
                </div>
            </div>

            <x-card class="report-audit-card" id="audit-log">
                <div class="report-card-header">
                    <h2>Riwayat Perubahan (Audit Log)</h2>
                </div>
                <form method="GET" action="{{ route('reports.history') }}" class="report-audit-filter">
                    <input type="hidden" name="period" value="{{ $period->format('Y-m') }}">
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                    <label>
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m21 21-4.5-4.5M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                        </svg>
                        <input name="history_search" value="{{ request('history_search') }}" placeholder="Cari transaksi atau catatan...">
                    </label>
                    <select name="history_action">
                        <option value="">Filter Aksi</option>
                        <option value="create" @selected(request('history_action') === 'create')>Create</option>
                        <option value="update" @selected(request('history_action') === 'update')>Update</option>
                        <option value="delete" @selected(request('history_action') === 'delete')>Delete</option>
                    </select>
                    <button type="submit">Filter</button>
                </form>

                <div class="report-table-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Transaksi</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($histories as $history)
                                @php
                                    $tone = $historyTone[$history->action] ?? 'update';
                                @endphp
                                <tr class="{{ $selectedHistory?->id === $history->id ? 'is-selected' : '' }}">
                                    <td data-label="Waktu">
                                        {{ $history->created_at?->translatedFormat('d M Y') }}
                                        <span>{{ $history->created_at?->format('H:i') }} WIB</span>
                                    </td>
                                    <td data-label="User">
                                        <span class="report-user-avatar">{{ str($history->user?->name ?? 'U')->substr(0, 2)->upper() }}</span>
                                    </td>
                                    <td data-label="Aksi">
                                        <span class="report-action-badge report-action-{{ $tone }}">{{ ucfirst($history->action) }}</span>
                                    </td>
                                    <td data-label="Transaksi">
                                        <a href="{{ route('reports.history', array_merge(request()->except('history_id'), ['history_id' => $history->id])) }}">
                                            {{ $history->transaction?->title ?? '-' }}
                                        </a>
                                    </td>
                                    <td data-label="Catatan">{{ $history->note ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="report-empty">Belum ada riwayat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="report-pagination">
                    <span>Menampilkan {{ $histories->firstItem() ?? 0 }} - {{ $histories->lastItem() ?? 0 }} dari {{ $histories->total() }} riwayat</span>
                    {{ $histories->links() }}
                </div>
            </x-card>

            <aside class="report-detail-panel">
                <div class="report-detail-header">
                    <h2>Detail Perubahan</h2>
                    <a href="{{ route('reports.history', request()->except('history_id')) }}" title="Tutup">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </a>
                </div>
                @if ($selectedHistory)
                    @php
                        $detailTone = $historyTone[$selectedHistory->action] ?? 'update';
                    @endphp
                    <div class="report-detail-body">
                        <div>
                            <span class="report-action-badge report-action-{{ $detailTone }}">{{ ucfirst($selectedHistory->action) }}</span>
                            <strong>{{ $selectedHistory->transaction?->title ?? 'Transaksi' }}</strong>
                            <p>{{ $selectedHistory->created_at?->translatedFormat('d M Y, H:i') }} WIB</p>
                        </div>
                        <section>
                            <h3>User</h3>
                            <div class="report-detail-user">
                                <span class="report-user-avatar">{{ str($selectedHistory->user?->name ?? 'U')->substr(0, 2)->upper() }}</span>
                                <p>{{ $selectedHistory->user?->name ?? 'Sistem' }}</p>
                            </div>
                        </section>
                        <section>
                            <h3>Catatan</h3>
                            <p>{{ $selectedHistory->note ?: '-' }}</p>
                        </section>
                        <section>
                            <h3>Ringkasan Perubahan</h3>
                            <div class="report-change-summary">
                                <span>Sebelum: {{ $selectedHistory->old_data ? 'Ada data' : '-' }}</span>
                                <span>Sesudah: {{ $selectedHistory->new_data ? 'Baru dibuat' : '-' }}</span>
                            </div>
                        </section>
                        <section>
                            <h3>Data Sebelum (Before)</h3>
                            <pre>{{ json_encode($selectedHistory->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: 'null' }}</pre>
                        </section>
                        <section>
                            <h3>Data Sesudah (After)</h3>
                            <pre class="is-after">{{ json_encode($selectedHistory->new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: 'null' }}</pre>
                        </section>
                        <section class="report-extra-info">
                            <h3>Informasi Tambahan</h3>
                            <p><span>IP Address</span><strong>103.23.45.67</strong></p>
                            <p><span>Device</span><strong>Chrome on Windows</strong></p>
                        </section>
                    </div>
                @else
                    <p class="report-empty">Pilih riwayat untuk melihat detail.</p>
                @endif
            </aside>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.Chart) {
                return;
            }

            const series = @js($monthlySeries);
            const labels = series.map((item) => item.label);
            const income = series.map((item) => item.income);
            const expense = series.map((item) => item.expense);
            const net = series.map((item) => item.net);
            const expenseLabels = @js($expenseByCategory->keys()->take(6)->values());
            const expenseValues = @js($expenseByCategory->values()->take(6)->values());
            const palette = @js($expensePalette);
            const format = (value) => `Rp ${Number(value).toLocaleString('id-ID')}`;

            const barEl = document.getElementById('reportIncomeExpenseChart');
            if (barEl) {
                new window.Chart(barEl, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Pemasukan', data: income, backgroundColor: '#10B981', borderRadius: 7, maxBarThickness: 30 },
                            { label: 'Pengeluaran', data: expense, backgroundColor: '#EF4444', borderRadius: 7, maxBarThickness: 30 },
                        ],
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: (context) => `${context.dataset.label}: ${format(context.raw)}` } },
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: {
                                grid: { color: '#E2E8F0', borderDash: [5, 5] },
                                ticks: { callback: (value) => value >= 1000000 ? `Rp ${value / 1000000} jt` : `Rp ${value}` },
                            },
                        },
                    },
                });
            }

            const donutEl = document.getElementById('reportExpenseDonut');
            if (donutEl) {
                new window.Chart(donutEl, {
                    type: 'doughnut',
                    data: {
                        labels: expenseLabels.length ? expenseLabels : ['Belum ada data'],
                        datasets: [{ data: expenseValues.length ? expenseValues : [1], backgroundColor: palette, borderWidth: 0 }],
                    },
                    options: {
                        cutout: '62%',
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: { legend: { display: false } },
                    },
                });
            }

            const cashflowEl = document.getElementById('reportCashflowChart');
            if (cashflowEl) {
                new window.Chart(cashflowEl, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Pemasukan', data: income, borderColor: '#10B981', backgroundColor: 'rgba(16,185,129,.1)', tension: .35, fill: true, pointRadius: 0 },
                            { label: 'Pengeluaran', data: expense, borderColor: '#EF4444', backgroundColor: 'rgba(239,68,68,.08)', tension: .35, fill: true, pointRadius: 0 },
                            { label: 'Saldo Bersih', data: net, borderColor: '#2D8CFF', backgroundColor: 'rgba(45,140,255,.1)', tension: .35, fill: false, pointRadius: 0 },
                        ],
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: (context) => `${context.dataset.label}: ${format(context.raw)}` } },
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: {
                                grid: { color: '#E2E8F0', borderDash: [5, 5] },
                                ticks: { callback: (value) => value >= 1000000 ? `Rp ${value / 1000000} jt` : `Rp ${value}` },
                            },
                        },
                    },
                });
            }
        });
    </script>
@endpush
