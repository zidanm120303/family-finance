@extends('layouts.app')

@php
    $formatCurrency = fn($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
@endphp

@section('title', 'Laporan & Riwayat - FamFinance')
@section('page_title', 'Laporan & Riwayat')
@section('page_subtitle', 'Pantau kinerja keuangan keluarga dan riwayat perubahan data')

@section('content')
    <div class="page-stack" x-data="{ tab: @js(request('tab', 'report')) }">
        <div class="flex gap-2 border-b border-slate-200">
            <button type="button" @click="tab = 'report'" class="border-b-2 px-4 pb-3 text-xs font-bold transition"
                :class="tab === 'report' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500'">Laporan</button>
            <button type="button" @click="tab = 'history'" class="border-b-2 px-4 pb-3 text-xs font-bold transition"
                :class="tab === 'history' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500'">Riwayat
                Perubahan</button>
        </div>

        <form method="GET" action="{{ route('reports.history') }}"
            class="grid min-w-0 gap-3 xl:grid-cols-[180px_180px_180px_minmax(170px,1fr)_auto] xl:items-end">
            <h2 class="sr-only">Filter Laporan</h2>
            <input type="hidden" name="tab" :value="tab">
            <label class="form-label">Periode<input name="period" type="month" value="{{ $period->format('Y-m') }}"
                    class="form-control"></label>
            <label class="form-label">Dari<input name="from" type="date" value="{{ $from->toDateString() }}"
                    class="form-control"></label>
            <label class="form-label">Sampai<input name="to" type="date" value="{{ $to->toDateString() }}"
                    class="form-control"></label>
            <label class="form-label">Kategori
                <select name="category_id" class="form-control">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) request('category_id') === $category->id)>{{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </label>
            <div class="grid grid-cols-3 gap-2">
                <button class="primary-action">Terapkan</button>
                <a href="{{ route('reports.export-pdf', request()->query()) }}" class="danger-action">PDF</a>
                <a href="{{ route('reports.export-excel', request()->query()) }}"
                    class="secondary-action text-emerald-700">Excel</a>
            </div>
        </form>

        <section x-show="tab === 'report'" x-cloak class="grid min-w-0 items-start gap-4 xl:grid-cols-12">
            <div class="grid min-w-0 gap-4 xl:col-span-4">
                <x-card class="p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-sm font-extrabold">Pemasukan vs Pengeluaran</h2>
                            <div class="mt-2 flex gap-3 text-[9px] text-slate-500"><span>● Pemasukan</span><span
                                    class="text-rose-500">● Pengeluaran</span></div>
                        </div><span class="text-slate-400">ⓘ</span>
                    </div>
                    <div class="mt-3 h-56"><canvas id="reportIncomeExpenseChart"></canvas></div>
                </x-card>
                <x-card class="p-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-extrabold">Pengeluaran per Kategori</h2><span
                            class="text-slate-400">ⓘ</span>
                    </div>
                    <div class="mt-3 h-56"><canvas id="reportExpenseChart"></canvas></div>
                </x-card>
                <x-card class="p-4">
                    <h2 class="text-sm font-extrabold">Arus Kas Bulanan</h2>
                    <div class="mt-3 h-48"><canvas id="reportCashflowChart"></canvas></div>
                </x-card>
            </div>

            <x-card class="overflow-hidden xl:col-span-8">
                <div class="ff-card-header">
                    <div>
                        <h2 class="text-sm font-extrabold">Riwayat Perubahan (Audit Log)</h2>
                        <p class="ff-muted mt-1">{{ $histories->total() }} aktivitas tercatat</p>
                    </div>
                </div>
                <form method="GET" action="{{ route('reports.history') }}"
                    class="grid gap-2 border-b border-slate-100 p-3 sm:grid-cols-[minmax(0,1fr)_130px_auto]">
                    <input type="hidden" name="tab" value="report"><input type="hidden" name="period"
                        value="{{ $period->format('Y-m') }}"><input type="hidden" name="from"
                        value="{{ $from->toDateString() }}"><input type="hidden" name="to"
                        value="{{ $to->toDateString() }}">
                    <input name="history_search" value="{{ request('history_search') }}" class="form-control"
                        placeholder="Cari transaksi atau catatan...">
                    <select name="history_action" class="form-control">
                        <option value="">Semua Aksi</option>
                        <option value="create" @selected(request('history_action') === 'create')>Create</option>
                        <option value="update" @selected(request('history_action') === 'update')>Update</option>
                        <option value="delete" @selected(request('history_action') === 'delete')>Delete</option>
                    </select>
                    <button class="secondary-action">Cari</button>
                </form>
                <div class="ff-table-wrap">
                    <table class="ff-table ff-compact-table min-w-[480px]">
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
                            @forelse($histories as $history)
                                <tr class="{{ $selectedHistory?->id === $history->id ? 'bg-emerald-50/60' : '' }}">
                                    <td class="whitespace-nowrap"><a
                                            href="{{ route('reports.history', array_merge(request()->query(), ['history_id' => $history->id, 'tab' => 'report'])) }}"
                                            class="block font-semibold">{{ $history->created_at?->translatedFormat('d M Y') }}<span
                                                class="mt-1 block text-[9px] text-slate-400">{{ $history->created_at?->format('H:i') }}
                                                WIB</span></a></td>
                                    <td class="whitespace-nowrap">{{ $history->user?->name ?? '-' }}</td>
                                    <td><span
                                            class="rounded-lg px-2 py-1 text-[9px] font-bold {{ $history->action === 'delete' ? 'bg-rose-50 text-rose-600' : ($history->action === 'create' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600') }}">{{ ucfirst($history->action) }}</span>
                                    </td>
                                    <td class="max-w-36 truncate font-bold text-slate-800">
                                        {{ $history->transaction?->title ?? 'Transaksi dihapus' }}</td>
                                    <td class="max-w-40 text-[10px] leading-4">{{ $history->note ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="ff-empty">Tidak ada histori yang cocok.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-4 py-3">{{ $histories->links() }}</div>
            </x-card>

            {{-- <x-card class="p-4 xl:sticky xl:top-[108px] xl:col-span-3">
                <div class="flex items-center justify-between"><h2 class="text-sm font-extrabold">Detail Perubahan</h2><span class="text-slate-400">×</span></div>
                @if ($selectedHistory)
                    <div class="mt-4">
                        <div class="flex items-center gap-2"><span class="rounded-lg bg-blue-50 px-2 py-1 text-[9px] font-bold text-blue-600">{{ ucfirst($selectedHistory->action) }}</span><b class="truncate text-xs">{{ $selectedHistory->transaction?->title ?? 'Transaksi' }}</b></div>
                        <p class="mt-2 text-[10px] text-slate-500">{{ $selectedHistory->created_at?->translatedFormat('d M Y, H:i') }} WIB</p>
                        <dl class="mt-4 grid gap-4 text-[10px]">
                            <div><dt class="font-bold text-slate-500">User</dt><dd class="mt-2 font-semibold">{{ $selectedHistory->user?->name ?? '-' }}</dd></div>
                            <div><dt class="font-bold text-slate-500">Catatan</dt><dd class="mt-2 leading-5">{{ $selectedHistory->note ?? '-' }}</dd></div>
                        </dl>
                        <div class="mt-4"><b class="text-[10px]">Data Sebelum</b><pre class="mt-2 max-h-32 overflow-auto rounded-xl bg-slate-50 p-3 text-[9px] leading-4 text-slate-600">{{ json_encode($selectedHistory->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></div>
                        <div class="mt-4"><b class="text-[10px]">Data Sesudah</b><pre class="mt-2 max-h-72 overflow-auto rounded-xl border border-emerald-100 bg-emerald-50/50 p-3 text-[9px] leading-4 text-emerald-800">{{ json_encode($selectedHistory->new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></div>
                    </div>
                @else
                    <div class="ff-empty mt-4">Pilih salah satu histori untuk melihat detail.</div>
                @endif
            </x-card> --}}
        </section>

        <section x-show="tab === 'history'" x-cloak>
            <x-card class="overflow-hidden">
                <div class="ff-card-header">
                    <div>
                        <h2 class="section-heading">Riwayat Perubahan</h2>
                        <p class="ff-muted mt-1">Audit aktivitas transaksi keluarga.</p>
                    </div><span
                        class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold">{{ $histories->total() }}
                        catatan</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($histories as $history)
                        <a href="{{ route('reports.history', array_merge(request()->query(), ['history_id' => $history->id, 'tab' => 'report'])) }}"
                            class="grid gap-2 p-4 transition hover:bg-slate-50 sm:grid-cols-[140px_100px_minmax(0,1fr)_180px] sm:items-center">
                            <span
                                class="text-[10px] font-semibold">{{ $history->created_at?->translatedFormat('d M Y H:i') }}</span>
                            <span class="text-[10px] font-bold text-emerald-700">{{ ucfirst($history->action) }}</span>
                            <span
                                class="truncate text-xs font-bold">{{ $history->transaction?->title ?? 'Transaksi dihapus' }}</span>
                            <span class="truncate text-[10px] text-slate-500">{{ $history->user?->name ?? '-' }}</span>
                        </a>
                    @empty
                        <div class="ff-empty m-4">Belum ada riwayat.</div>
                    @endforelse
                </div>
                <div class="border-t border-slate-100 p-4">{{ $histories->links() }}</div>
            </x-card>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.Chart) return;
            const labels = @json($monthlySeries->pluck('label')),
                income = @json($monthlySeries->pluck('income')),
                expense = @json($monthlySeries->pluck('expense')),
                net = @json($monthlySeries->pluck('net'));
            const bar = document.getElementById('reportIncomeExpenseChart');
            if (bar) new window.Chart(bar, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Pemasukan',
                        data: income,
                        backgroundColor: '#10b981',
                        borderRadius: 5,
                        barThickness: 12
                    }, {
                        label: 'Pengeluaran',
                        data: expense,
                        backgroundColor: '#ef4444',
                        borderRadius: 5,
                        barThickness: 12
                    }]
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
                                callback: v => Number(v / 1000000) + 'jt',
                                font: {
                                    size: 9
                                }
                            },
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 9
                                }
                            }
                        }
                    }
                }
            });
            const pie = document.getElementById('reportExpenseChart');
            if (pie) new window.Chart(pie, {
                type: 'doughnut',
                data: {
                    labels: @json($expenseByCategory->keys()->values()),
                    datasets: [{
                        data: @json($expenseByCategory->values()->values()),
                        backgroundColor: ['#fb7185', '#8b5cf6', '#06b6d4', '#10b981', '#3b82f6',
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
                                usePointStyle: true,
                                boxWidth: 8,
                                font: {
                                    size: 9
                                }
                            }
                        }
                    }
                }
            });
            const line = document.getElementById('reportCashflowChart');
            if (line) new window.Chart(line, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Net',
                        data: net,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,.1)',
                        fill: true,
                        tension: .35,
                        pointRadius: 2
                    }]
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
                            ticks: {
                                callback: v => Number(v / 1000000) + 'jt',
                                font: {
                                    size: 9
                                }
                            },
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 9
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
