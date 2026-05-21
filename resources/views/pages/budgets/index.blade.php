@extends('layouts.app')
@section('page_title', 'Anggaran')
@section('page_subtitle', 'Kelola dan pantau anggaran keluarga Anda dengan mudah.')
@section('content')
    @php
        $formatCurrency = fn($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
        $budgetPayload = $budgets
            ->map(
                fn($budget) => [
                    'id' => $budget['model']->id,
                    'category_id' => $budget['model']->category_id,
                    'category_name' => $budget['category']?->category_name ?? 'Lainnya',
                    'month' => $budget['model']->month,
                    'year' => $budget['model']->year,
                    'limit_amount' => (float) $budget['limit'],
                    'update_url' => route('budgets.update', $budget['model']),
                ],
            )
            ->values();
        $firstCategoryId = $categories->first()?->id;
    @endphp

    <div class="budget-page" x-data="budgetPage({
        storeUrl: @js(route('budgets.store')),
        budgets: @js($budgetPayload),
        defaultMonth: @js($month),
        defaultYear: @js($year),
        firstCategoryId: @js($firstCategoryId),
    })">
        <section class="budget-stat-grid">
            <x-card class="budget-stat-card">
                <span class="budget-stat-icon budget-stat-wallet">
                    <img src="{{ asset('assets/svg/icon-wallet.svg') }}" alt="">
                </span>
                <div>
                    <span>Total Anggaran</span>
                    <strong>{{ $formatCurrency($totalBudget) }}</strong>
                    <small>Total batas anggaran bulan ini</small>
                </div>
                <i>i</i>
            </x-card>

            <x-card class="budget-stat-card">
                <span class="budget-stat-icon budget-stat-spent">
                    <img src="{{ asset('assets/svg/icon-expense.svg') }}" alt="">
                </span>
                <div>
                    <span>Terpakai</span>
                    <strong>{{ $formatCurrency($totalSpent) }}</strong>
                    <small class="text-rose-600">{{ $spentPercentage }}% dari total anggaran</small>
                </div>
                <i>i</i>
            </x-card>

            <x-card class="budget-stat-card">
                <span class="budget-stat-icon budget-stat-remaining">
                    <img src="{{ asset('assets/svg/icon-budget.svg') }}" alt="">
                </span>
                <div>
                    <span>Sisa Anggaran</span>
                    <strong>{{ $formatCurrency(max(0, $remainingBudget)) }}</strong>
                    <small class="text-amber-600">{{ $remainingPercentage }}% dari total anggaran</small>
                </div>
                <i>i</i>
            </x-card>

            <x-card class="budget-stat-card">
                <span class="budget-stat-icon budget-stat-alert">
                    <img src="{{ asset('assets/svg/icon-shield.svg') }}" alt="">
                </span>
                <div>
                    <span>Kategori Melebihi Batas</span>
                    <strong>{{ $overLimitCount }}</strong>
                    <small>Perlu perhatian</small>
                </div>
                <i>i</i>
            </x-card>
        </section>

        <form method="GET" action="{{ route('budgets.index') }}" class="budget-toolbar">
            <label>
                <span>Bulan</span>
                <select name="month">
                    @foreach ($months as $monthValue => $monthLabel)
                        <option value="{{ $monthValue }}" @selected((int) $month === (int) $monthValue)>{{ $monthLabel }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Tahun</span>
                <select name="year">
                    @foreach ($years as $yearValue)
                        <option value="{{ $yearValue }}" @selected((int) $year === (int) $yearValue)>{{ $yearValue }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="budget-filter-button">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 5h16l-6.5 7.5V19l-3 1v-7.5L4 5Z" />
                </svg>
                Filter
            </button>
            <div class="budget-toolbar-spacer"></div>
            <a href="{{ route('reports.export-pdf') }}" class="budget-soft-button">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14" />
                </svg>
                Unduh Laporan
            </a>
            <button type="button" class="budget-primary-button" @click="startCreate()">
                <span class="budget-plus">+</span>
                Buat Anggaran Baru
            </button>
        </form>

        <x-card class="budget-editor-card" x-show="showForm" x-cloak>
            <div class="budget-card-header">
                <h2 x-text="formMode === 'create' ? 'Buat Anggaran Baru' : 'Edit Anggaran'"></h2>
                <button type="button" class="budget-close-button" @click="showForm = false">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 6l12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>
            <form method="POST" :action="formAction()" class="budget-editor-form">
                @csrf
                <template x-if="formMode === 'edit'">
                    <input type="hidden" name="_method" value="PATCH">
                </template>
                <label>
                    <span>Kategori</span>
                    <select name="category_id" x-model="form.category_id" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Bulan</span>
                    <select name="month" x-model="form.month" required>
                        @foreach ($months as $monthValue => $monthLabel)
                            <option value="{{ $monthValue }}">{{ $monthLabel }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Tahun</span>
                    <select name="year" x-model="form.year" required>
                        @foreach ($years as $yearValue)
                            <option value="{{ $yearValue }}">{{ $yearValue }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Batas Anggaran</span>
                    <input name="limit_amount" type="number" min="1" step="1000" x-model="form.limit_amount"
                        required>
                </label>
                <button type="submit" class="budget-primary-button">
                    <span x-text="formMode === 'create' ? 'Simpan Anggaran' : 'Simpan Perubahan'"></span>
                </button>
            </form>
        </x-card>

        <section class="budget-content-grid">
            <x-card class="budget-table-card">
                <div class="budget-card-header">
                    <h2>Daftar Anggaran per Kategori</h2>
                </div>
                <div class="budget-table-wrap">
                    <table class="budget-table">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th>Batas Anggaran</th>
                                <th>Realisasi</th>
                                <th>Sisa</th>
                                <th>Progres</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($budgets as $budget)
                                @php
                                    $category = $budget['category'];
                                    $categoryColor = $category?->color ?: '#10B981';
                                    $categoryBg = preg_match('/^#[0-9A-Fa-f]{6}$/', $categoryColor)
                                        ? $categoryColor . '18'
                                        : '#F8FAFC';
                                    $categoryIcon = $category?->icon ?: 'icon-budget.svg';
                                    $statusTone = $budget['status']['tone'];
                                @endphp
                                <tr>
                                    <td data-label="Kategori">
                                        <div class="budget-category-cell">
                                            <span
                                                style="--budget-color: {{ $categoryColor }}; --budget-bg: {{ $categoryBg }};">
                                                <img src="{{ asset('assets/svg/' . $categoryIcon) }}" alt="">
                                            </span>
                                            <strong>{{ $category?->category_name ?? 'Lainnya' }}</strong>
                                        </div>
                                    </td>
                                    <td data-label="Bulan">
                                        {{ $months[$budget['model']->month] ?? $budget['model']->month }}</td>
                                    <td data-label="Tahun">{{ $budget['model']->year }}</td>
                                    <td data-label="Batas Anggaran">{{ $formatCurrency($budget['limit']) }}</td>
                                    <td data-label="Realisasi">{{ $formatCurrency($budget['spent']) }}</td>
                                    <td data-label="Sisa"
                                        class="{{ $budget['remaining'] < 0 ? 'budget-money-danger' : '' }}">
                                        {{ $budget['remaining'] < 0 ? '-' : '' }}{{ $formatCurrency(abs($budget['remaining'])) }}
                                    </td>
                                    <td data-label="Progres">
                                        <div class="budget-progress">
                                            <span>{{ $budget['percentage'] }}%</span>
                                            <div class="budget-progress-track budget-progress-{{ $statusTone }}">
                                                <i style="width: {{ min(100, $budget['percentage']) }}%"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Status">
                                        <span class="budget-status budget-status-{{ $statusTone }}">
                                            {{ $budget['status']['label'] }}
                                        </span>
                                    </td>
                                    <td data-label="Aksi">
                                        <div class="budget-actions">
                                            <button type="button"
                                                @click="editBudget({{ $budget['model']->id }})">Edit</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="budget-empty">Belum ada anggaran pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="budget-table-footer">
                    <span>Menampilkan 1 - {{ $budgets->count() }} dari {{ $budgets->count() }} kategori</span>
                    <div class="budget-pagination">
                        <button type="button" disabled>&lsaquo;</button>
                        <button type="button" class="is-active">1</button>
                        <button type="button" disabled>&rsaquo;</button>
                    </div>
                </div>
            </x-card>

            <div class="budget-side-stack">
                <x-card class="budget-chart-card">
                    <div class="budget-card-header">
                        <h2>Perbandingan Anggaran vs Realisasi</h2>
                        <span>{{ $period->translatedFormat('F') }}</span>
                    </div>
                    <div class="budget-chart-legend">
                        <span><i class="bg-emerald-500"></i>Batas Anggaran</span>
                        <span><i class="bg-blue-500"></i>Realisasi</span>
                    </div>
                    <div class="budget-chart-wrap">
                        <canvas id="budgetComparisonChart"></canvas>
                    </div>
                </x-card>

                <x-card class="budget-attention-card">
                    <div class="budget-card-header">
                        <h2>Kategori Perlu Perhatian</h2>
                        <a href="{{ route('reports.history') }}">Lihat semua</a>
                    </div>
                    <div class="budget-attention-list">
                        @forelse ($attentionBudgets->take(3) as $budget)
                            @php
                                $tone = $budget['status']['tone'];
                                $overspend = max(0, abs($budget['remaining']));
                            @endphp
                            <div class="budget-attention-row">
                                <span class="budget-attention-icon budget-attention-{{ $tone }}">!</span>
                                <div class="min-w-0 flex-1">
                                    <strong>{{ $budget['category']?->category_name ?? 'Lainnya' }}</strong>
                                    <p>
                                        {{ $tone === 'danger' ? 'Realisasi anggaran telah mencapai batas.' : 'Anggaran hampir mencapai batas.' }}
                                    </p>
                                    <div class="budget-progress-track budget-progress-{{ $tone }}">
                                        <i style="width: {{ min(100, $budget['percentage']) }}%"></i>
                                    </div>
                                </div>
                                <em>{{ $budget['percentage'] }}%</em>
                            </div>
                        @empty
                            <p class="budget-empty-small">Semua kategori masih aman.</p>
                        @endforelse
                    </div>
                </x-card>
            </div>
        </section>

        <x-card class="budget-tip-card">
            <span>
                <img src="{{ asset('assets/svg/icon-income.svg') }}" alt="">
            </span>
            <div>
                <h2>Tips Mengelola Anggaran</h2>
                <p>Tinjau anggaran secara rutin dan sesuaikan dengan kebutuhan keluarga agar keuangan tetap sehat.</p>
            </div>
            <a href="{{ route('reports.history') }}">Pelajari Lebih Lanjut &rarr;</a>
        </x-card>
    </div>
@endsection

@push('scripts')
    <script>
        window.budgetPage = (config) => ({
            showForm: false,
            formMode: 'create',
            storeUrl: config.storeUrl,
            budgets: config.budgets || [],
            form: {
                category_id: config.firstCategoryId,
                month: config.defaultMonth,
                year: config.defaultYear,
                limit_amount: '',
            },

            startCreate() {
                this.formMode = 'create';
                this.showForm = true;
                this.form = {
                    category_id: config.firstCategoryId,
                    month: config.defaultMonth,
                    year: config.defaultYear,
                    limit_amount: '',
                };
            },

            editBudget(id) {
                const budget = this.budgets.find((item) => item.id === id);

                if (!budget) {
                    this.startCreate();
                    return;
                }

                this.formMode = 'edit';
                this.showForm = true;
                this.form = {
                    id: budget.id,
                    category_id: budget.category_id,
                    month: budget.month,
                    year: budget.year,
                    limit_amount: budget.limit_amount,
                    update_url: budget.update_url,
                };
            },

            formAction() {
                return this.formMode === 'edit' ? this.form.update_url : this.storeUrl;
            },
        });

        document.addEventListener('DOMContentLoaded', () => {
            const chartEl = document.getElementById('budgetComparisonChart');

            if (!chartEl || !window.Chart) {
                return;
            }

            new window.Chart(chartEl, {
                type: 'bar',
                data: {
                    labels: @js($chartLabels),
                    datasets: [{
                            label: 'Batas Anggaran',
                            data: @js($chartLimits),
                            backgroundColor: '#10B981',
                            borderRadius: 6,
                            maxBarThickness: 28,
                        },
                        {
                            label: 'Realisasi',
                            data: @js($chartSpent),
                            backgroundColor: '#2D8CFF',
                            borderRadius: 6,
                            maxBarThickness: 28,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) =>
                                    `${context.dataset.label}: Rp ${Number(context.raw).toLocaleString('id-ID')}`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                        },
                        y: {
                            grid: {
                                color: '#E2E8F0',
                                borderDash: [5, 5],
                            },
                            ticks: {
                                callback: (value) => value >= 1000000 ? `Rp ${value / 1000000}jt` :
                                    `Rp ${value}`,
                            },
                        },
                    },
                },
            });
        });
    </script>
@endpush
