@extends('layouts.app')

@php
    $formatCurrency = fn ($value) => 'Rp '.number_format((float) $value, 0, ',', '.');
    $statusClasses = [
        'safe' => 'bg-emerald-50 text-emerald-700',
        'warning' => 'bg-amber-50 text-amber-700',
        'danger' => 'bg-rose-50 text-rose-700',
    ];
@endphp

@section('title', 'Anggaran - FamFinance')
@section('page_title', 'Anggaran')
@section('page_subtitle', 'Anggaran › '.$period->translatedFormat('F Y'))

@section('content')
    <div class="page-stack" x-data="{ addOpen: false }">
        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Total Anggaran" :value="$formatCurrency($totalBudget)" icon="icon-budget.svg" tone="blue" :hint="$budgets->count().' kategori aktif'" />
            <x-stat-card label="Total Terpakai" :value="$formatCurrency($totalSpent)" icon="icon-expense.svg" tone="rose" :hint="$spentPercentage.'% dari total anggaran'" />
            <x-stat-card label="Sisa Anggaran" :value="$formatCurrency($remainingBudget)" icon="icon-wallet.svg" tone="emerald" :hint="$remainingPercentage.'% masih tersedia'" />
            <x-stat-card label="Kategori Melebihi" :value="$overLimitCount" icon="icon-lightning.svg" tone="amber" hint="Perlu perhatian keluarga" />
        </section>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <form method="GET" action="{{ route('budgets.index') }}" class="grid gap-2 sm:grid-cols-[160px_120px_auto]">
                <label class="form-label">Bulan
                    <select name="month" class="form-control">
                        @foreach($months as $number => $name)
                            <option value="{{ $number }}" @selected((int) $number === (int) $month)>{{ $name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="form-label">Tahun
                    <select name="year" class="form-control">
                        @foreach($years as $yearOption)
                            <option value="{{ $yearOption }}" @selected((int) $yearOption === (int) $year)>{{ $yearOption }}</option>
                        @endforeach
                    </select>
                </label>
                <button class="secondary-action self-end">Terapkan</button>
            </form>
            <div class="flex gap-2">
                <a href="{{ route('reports.export-pdf', ['month' => $month, 'year' => $year]) }}" class="secondary-action">Export</a>
                <button type="button" @click="addOpen = true" class="primary-action">+ Tambah Anggaran</button>
            </div>
        </div>

        <section class="grid min-w-0 items-start gap-4 xl:grid-cols-[minmax(0,1fr)_300px]">
            <x-card class="overflow-hidden">
                <div class="ff-card-header">
                    <div>
                        <h2 class="section-heading">Daftar Anggaran</h2>
                        <p class="ff-muted mt-1">Pantau alokasi dan realisasi {{ $period->translatedFormat('F Y') }}.</p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-[10px] font-bold text-emerald-700">{{ $remainingPercentage }}% tersisa</span>
                </div>
                <div class="ff-table-wrap">
                    <table class="ff-table min-w-[820px]">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th class="text-right">Anggaran</th>
                                <th class="text-right">Terpakai</th>
                                <th class="text-right">Sisa</th>
                                <th class="min-w-48">Progress</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($budgets as $budget)
                                @php
                                    $tone = $budget['status']['tone'];
                                    $barClass = $tone === 'danger' ? 'bg-rose-500' : ($tone === 'warning' ? 'bg-amber-500' : 'bg-emerald-500');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-slate-50">
                                                <img src="{{ asset('assets/svg/'.($budget['category']?->icon ?: 'icon-budget.svg')) }}" class="h-5 w-5" alt="">
                                            </span>
                                            <b class="whitespace-nowrap text-slate-950">{{ $budget['category']?->category_name ?? 'Lainnya' }}</b>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap text-right font-bold text-slate-800">{{ $formatCurrency($budget['limit']) }}</td>
                                    <td class="whitespace-nowrap text-right font-bold text-rose-600">{{ $formatCurrency($budget['spent']) }}</td>
                                    <td class="whitespace-nowrap text-right font-bold {{ $budget['remaining'] < 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $formatCurrency($budget['remaining']) }}</td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="h-2 flex-1 rounded-full bg-slate-100"><div class="h-2 rounded-full {{ $barClass }}" style="width: {{ min(100, $budget['percentage']) }}%"></div></div>
                                            <b class="w-10 text-right text-[10px]">{{ $budget['percentage'] }}%</b>
                                        </div>
                                    </td>
                                    <td><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusClasses[$tone] ?? $statusClasses['safe'] }}">{{ $budget['status']['label'] }}</span></td>
                                    <td>
                                        <details class="group relative">
                                            <summary class="ff-icon-button mx-auto h-8 w-8 cursor-pointer list-none">⋮</summary>
                                            <div class="absolute right-0 z-20 mt-1 w-56 rounded-xl border border-slate-200 bg-white p-3 shadow-xl">
                                                <form method="POST" action="{{ route('budgets.update', $budget['model']) }}" class="grid gap-2">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="category_id" value="{{ $budget['model']->category_id }}">
                                                    <input type="hidden" name="month" value="{{ $month }}">
                                                    <input type="hidden" name="year" value="{{ $year }}">
                                                    <label class="form-label">Limit Baru
                                                        <input name="limit_amount" type="number" min="1" value="{{ (int) $budget['limit'] }}" class="form-control" required>
                                                    </label>
                                                    <button class="primary-action w-full">Update</button>
                                                </form>
                                                <form method="POST" action="{{ route('budgets.destroy', $budget['model']) }}" class="mt-2" onsubmit="return confirm('Hapus anggaran ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="danger-action w-full">Hapus</button>
                                                </form>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7"><div class="ff-empty">Belum ada anggaran pada periode ini.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <aside class="grid gap-4 md:grid-cols-2 xl:grid-cols-1">
                <x-card class="p-4">
                    <div class="flex items-center justify-between">
                        <div><h2 class="text-sm font-extrabold">Pengeluaran per Kategori</h2><p class="mt-1 text-[10px] text-slate-500">Anggaran vs realisasi</p></div>
                        <span class="text-slate-400">•••</span>
                    </div>
                    <div class="mt-3 h-56"><canvas id="budgetComparisonChart"></canvas></div>
                </x-card>
                <x-card class="p-4">
                    <h2 class="text-sm font-extrabold">Perlu Perhatian</h2>
                    <div class="mt-3 grid gap-2">
                        @forelse($attentionBudgets->take(4) as $budget)
                            <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-3">
                                <div class="flex items-center justify-between gap-2"><b class="truncate text-[11px]">{{ $budget['category']?->category_name ?? 'Lainnya' }}</b><span class="text-[10px] font-bold text-amber-700">{{ $budget['percentage'] }}%</span></div>
                                <p class="mt-1 text-[9px] leading-4 text-slate-500">{{ $budget['status']['message'] }}</p>
                            </div>
                        @empty
                            <p class="ff-empty py-6">Semua anggaran masih aman.</p>
                        @endforelse
                    </div>
                </x-card>
            </aside>
        </section>

        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 px-4 py-3 text-[11px] font-medium text-slate-600">
            <b class="text-emerald-800">Tips mengatur anggaran:</b> prioritaskan kebutuhan utama, tinjau kategori yang mendekati batas, lalu sesuaikan alokasi bersama keluarga.
        </div>

        <div x-show="addOpen" x-cloak>
            <button type="button" class="fixed inset-0 z-40 bg-slate-950/40" @click="addOpen = false" aria-label="Tutup panel"></button>
            <aside class="ff-drawer p-5" x-transition>
                <div class="flex items-center justify-between"><div><h2 class="section-heading">Tambah Anggaran</h2><p class="ff-muted mt-1">{{ $period->translatedFormat('F Y') }}</p></div><button type="button" class="ff-icon-button" @click="addOpen = false">×</button></div>
                <form method="POST" action="{{ route('budgets.store') }}" class="mt-6 grid gap-4">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <label class="form-label">Kategori Pengeluaran
                        <select name="category_id" class="form-control" required>
                            @foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->category_name }}</option>@endforeach
                        </select>
                    </label>
                    <label class="form-label">Limit Anggaran
                        <input name="limit_amount" type="number" min="1" class="form-control" placeholder="1500000" required>
                    </label>
                    <button class="primary-action w-full">Simpan Anggaran</button>
                </form>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('budgetComparisonChart');
    if (!el || !window.Chart) return;
    new window.Chart(el, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [
                { label: 'Limit', data: @json($chartLimits), backgroundColor: '#3b82f6', borderRadius: 6, barThickness: 8 },
                { label: 'Terpakai', data: @json($chartSpent), backgroundColor: '#ef4444', borderRadius: 6, barThickness: 8 },
            ],
        },
        options: {
            indexAxis: 'y', maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { display: false }, grid: { color: '#f1f5f9' } }, y: { grid: { display: false }, ticks: { font: { size: 9 } } } },
        },
    });
});
</script>
@endpush
