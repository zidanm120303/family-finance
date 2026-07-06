@extends('layouts.app')

@php
    $formatCurrency = fn($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    $walletTypeLabels = ['cash' => 'Tunai', 'bank' => 'Bank', 'e-wallet' => 'E-Wallet'];
    $walletTypeClasses = ['cash' => 'bg-emerald-500', 'bank' => 'bg-blue-600', 'e-wallet' => 'bg-violet-600'];
    $months = collect(range(1, 12))->mapWithKeys(
        fn($monthNumber) => [
            $monthNumber => \Carbon\Carbon::create($period->year, $monthNumber, 1)->translatedFormat('F'),
        ],
    );
    $years = range(now()->year - 2, now()->year + 2);
@endphp

@section('title', 'Dompet - FamFinance')
@section('page_title', 'Dompet')
@section('page_subtitle', 'Kelola semua dompet dan rekening keluarga Anda')

@section('content')
    <div class="page-stack" x-data="{ addOpen: false }">
        <h2 class="sr-only">Daftar Dompet</h2>
        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Total Saldo" :value="$formatCurrency($totalBalance)" icon="icon-wallet.svg" tone="emerald" :hint="$wallets->count() . ' dompet aktif'" />
            <x-stat-card label="Jumlah Dompet" :value="$wallets->count()" icon="icon-budget.svg" tone="purple" :hint="$bankCount . ' bank · ' . $ewalletCount . ' e-wallet'" />
            <x-stat-card label="Saldo Bank" :value="$formatCurrency($bankBalance)" icon="icon-shield.svg" tone="blue" :hint="$bankPercentage . '% dari total saldo'" />
            <x-stat-card label="Saldo E-Wallet" :value="$formatCurrency($ewalletBalance)" icon="icon-wallet.svg" tone="amber" :hint="$ewalletPercentage . '% dari total saldo'" />
        </section>

        <section class="grid min-w-0 items-stretch gap-4 xl:grid-cols-12">
            <x-card class="p-4 xl:col-span-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold">Arus Saldo (Semua Dompet)</h2>
                        <div class="mt-2 flex gap-4 text-[10px] font-medium text-slate-500">
                            <span class="flex items-center gap-2"><i
                                    class="h-2 w-2 rounded-full bg-emerald-500"></i>Masuk</span>
                            <span class="flex items-center gap-2"><i
                                    class="h-2 w-2 rounded-full bg-rose-500"></i>Keluar</span>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('wallets.index') }}" class="grid grid-cols-[110px_84px] gap-2">
                        <select name="month" class="form-control h-9 px-2 text-[10px]" onchange="this.form.submit()">
                            @foreach ($months as $number => $name)
                                <option value="{{ $number }}" @selected((int) $number === (int) $period->month)>{{ $name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="year" class="form-control h-9 px-2 text-[10px]" onchange="this.form.submit()">
                            @foreach ($years as $yearOption)
                                <option value="{{ $yearOption }}" @selected((int) $yearOption === (int) $period->year)>{{ $yearOption }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="mt-3 h-72"><canvas id="walletCashflowChart"></canvas></div>
            </x-card>

            <x-card class="p-4 xl:col-span-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-extrabold">Dompet Saya</h2><button type="button" @click="addOpen = true"
                        class="text-[11px] font-bold text-emerald-600">Kelola</button>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    @forelse($wallets as $wallet)
                        <details class="group rounded-xl border border-slate-200 bg-white p-3.5 transition open:shadow-lg">
                            <summary class="cursor-pointer list-none">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex min-w-0 items-center gap-3">
                                        {{-- <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl text-xs font-extrabold text-white {{ $walletTypeClasses[$wallet->type] ?? 'bg-slate-500' }}">
                                            {{ $wallet->type === 'bank' ? '▦' : ($wallet->type === 'e-wallet' ? '●' : '◉') }}
                                        </span> --}}
                                        <div class="min-w-0">
                                            <b class="block truncate text-xs">{{ $wallet->wallet_name }}</b>
                                            <span
                                                class="mt-1 block text-[10px] text-slate-500">{{ $walletTypeLabels[$wallet->type] ?? $wallet->type }}</span>
                                        </div>
                                    </div>
                                    <span class="text-slate-400">›</span>
                                </div>
                                <strong
                                    class="mt-3 block text-base font-extrabold">{{ $formatCurrency($wallet->balance) }}</strong>
                                <span
                                    class="mt-1 block truncate text-[10px] text-slate-500">{{ $wallet->account_number ?: $wallet->transactions_count . ' transaksi' }}</span>
                            </summary>
                            <div class="mt-4 border-t border-slate-100 pt-3">
                                <form method="POST" action="{{ route('wallets.update', $wallet) }}" class="grid gap-2">
                                    @csrf @method('PUT')
                                    <input name="wallet_name" value="{{ $wallet->wallet_name }}" class="form-control"
                                        required>
                                    <div class="grid grid-cols-2 gap-2">
                                        <select name="type" class="form-control">
                                            <option value="cash" @selected($wallet->type === 'cash')>Cash</option>
                                            <option value="bank" @selected($wallet->type === 'bank')>Bank</option>
                                            <option value="e-wallet" @selected($wallet->type === 'e-wallet')>E-Wallet</option>
                                        </select>
                                        <input name="balance" type="number" min="0"
                                            value="{{ (int) $wallet->balance }}" class="form-control" required>
                                    </div>
                                    <input name="account_number" value="{{ $wallet->account_number }}" class="form-control"
                                        placeholder="Nomor akun">
                                    <button class="primary-action w-full">Simpan</button>
                                </form>
                                <form method="POST" action="{{ route('wallets.destroy', $wallet) }}" class="mt-2"
                                    onsubmit="return confirm('Hapus dompet ini?')">
                                    @csrf @method('DELETE')
                                    <button class="danger-action w-full">Hapus</button>
                                </form>
                            </div>
                        </details>
                    @empty
                        <div class="ff-empty sm:col-span-2">Belum ada dompet.</div>
                    @endforelse
                </div>
            </x-card>

            <div class="grid content-start gap-3 xl:col-span-2">
                <x-card class="p-4">
                    <h2 class="text-sm font-extrabold">Aksi Cepat</h2>
                    <button type="button" @click="addOpen = true"
                        class="mt-3 flex w-full items-center gap-3 rounded-xl bg-emerald-600 p-3 text-left text-white">
                        <span class="text-xl">＋</span><span><b class="block text-xs">Tambah Dompet</b><span
                                class="mt-1 block text-[9px] opacity-80">Bank atau e-wallet baru</span></span>
                    </button>
                    <a href="{{ route('transactions.create') }}"
                        class="mt-2 flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50 p-3 text-blue-700">
                        <span class="text-lg">⇄</span><span><b class="block text-xs">Catat Transaksi</b><span
                                class="mt-1 block text-[9px] text-slate-500">Perbarui saldo dompet</span></span>
                    </a>
                    <a href="{{ route('reports.history') }}"
                        class="mt-2 flex items-center gap-3 rounded-xl border border-violet-100 bg-violet-50 p-3 text-violet-700">
                        <span class="text-lg">▤</span><span><b class="block text-xs">Rekonsiliasi</b><span
                                class="mt-1 block text-[9px] text-slate-500">Periksa riwayat mutasi</span></span>
                    </a>
                </x-card>
                <div
                    class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4 text-[10px] font-medium leading-5 text-slate-500">
                    <b class="block text-xs text-slate-800">💡 Tips Keuangan</b>
                    <p class="mt-2">Lakukan rekonsiliasi secara rutin agar catatan keuangan selalu akurat.</p>
                </div>
            </div>
        </section>

        <x-card class="overflow-hidden">
            <div class="ff-card-header">
                <div>
                    <h2 class="section-heading">Aktivitas Dompet Terbaru</h2>
                    <p class="ff-muted mt-1">Lima transaksi terbaru dari seluruh dompet.</p>
                </div><a href="{{ route('transactions.index') }}" class="text-[11px] font-bold text-emerald-600">Lihat
                    semua</a>
            </div>
            <div class="ff-table-wrap">
                <table class="ff-table min-w-[760px]">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Dompet</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                            <th class="text-right">Masuk</th>
                            <th class="text-right">Keluar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivities as $activity)
                            <tr>
                                <td class="whitespace-nowrap">
                                    {{ $activity->transaction_date?->translatedFormat('d M Y') }} <span
                                        class="ml-2 text-[9px] text-slate-400">{{ $activity->created_at?->format('H:i') }}</span>
                                </td>
                                <td class="font-bold">{{ $activity->wallet?->wallet_name ?? '-' }}</td>
                                <td>{{ $activity->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                                <td>{{ $activity->title }} <span class="text-slate-400">—
                                        {{ $activity->user?->name }}</span></td>
                                <td class="text-right font-bold text-emerald-600">
                                    {{ $activity->type === 'income' ? $formatCurrency($activity->amount) : '-' }}</td>
                                <td class="text-right font-bold text-rose-600">
                                    {{ $activity->type === 'expense' ? $formatCurrency($activity->amount) : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="ff-empty">Belum ada aktivitas dompet.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <div x-show="addOpen" x-cloak>
            <button type="button" class="fixed inset-0 z-40 bg-slate-950/40" @click="addOpen = false"
                aria-label="Tutup panel"></button>
            <aside class="ff-drawer p-5" x-transition>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="section-heading">Tambah Dompet</h2>
                        <p class="ff-muted mt-1">Tambahkan sumber dana keluarga.</p>
                    </div><button type="button" class="ff-icon-button" @click="addOpen = false">×</button>
                </div>
                <form method="POST" action="{{ route('wallets.store') }}" class="mt-6 grid gap-4">
                    @csrf
                    <label class="form-label">Nama Dompet<input name="wallet_name" value="{{ old('wallet_name') }}"
                            class="form-control" placeholder="Cash, BCA, GoPay" required></label>
                    <label class="form-label">Tipe<select name="type" class="form-control">
                            <option value="cash">Cash</option>
                            <option value="bank">Bank</option>
                            <option value="e-wallet">E-Wallet</option>
                        </select></label>
                    <label class="form-label">Saldo Awal<input name="balance" type="number" min="0"
                            value="{{ old('balance', 0) }}" class="form-control" required></label>
                    <label class="form-label">Nomor Akun<input name="account_number" value="{{ old('account_number') }}"
                            class="form-control" placeholder="Opsional"></label>
                    <button class="primary-action w-full">Simpan Dompet</button>
                </form>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('walletCashflowChart');
            if (!el || !window.Chart) return;
            new window.Chart(el, {
                type: 'line',
                data: {
                    labels: @json($cashflow['labels']),
                    datasets: [{
                            label: 'Masuk',
                            data: @json($cashflow['income']),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16,185,129,.10)',
                            fill: true,
                            tension: .35,
                            pointRadius: 0,
                            borderWidth: 2
                        },
                        {
                            label: 'Keluar',
                            data: @json($cashflow['expense']),
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239,68,68,.08)',
                            fill: true,
                            tension: .35,
                            pointRadius: 0,
                            borderWidth: 2
                        },
                    ],
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
                                color: '#e2e8f0',
                                borderDash: [4, 4]
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
        });
    </script>
@endpush
