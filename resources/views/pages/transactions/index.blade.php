@extends('layouts.app')

@php
    $formatCurrency = fn($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
@endphp

@section('title', 'Transaksi - FamFinance')
@section('page_title', 'Transaksi')
@section('page_subtitle', 'Transaksi')

@section('content')
    <div class="page-stack">
        <form method="GET" action="{{ route('transactions.index') }}" class="grid gap-3">
            <div class="grid min-w-0 gap-3 xl:grid-cols-[minmax(250px,1fr)_minmax(310px,.9fr)_auto]">
                <label class="relative min-w-0">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500"
                        viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" />
                        <path d="m20 20-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <input name="search" value="{{ request('search') }}" class="form-control pl-10"
                        placeholder="Cari judul atau kode transaksi...">
                </label>

                <div class="grid min-w-0 grid-cols-2 gap-2">
                    <label class="relative min-w-0">
                        <span
                            class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400">DARI</span>
                        <input name="date_from" type="date" value="{{ request('date_from') }}"
                            class="form-control pl-14">
                    </label>
                    <label class="relative min-w-0">
                        <span
                            class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400">S/D</span>
                        <input name="date_to" type="date" value="{{ request('date_to') }}" class="form-control pl-12">
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                    <a href="{{ route('transactions.create') }}" class="primary-action">
                        <span class="text-lg leading-none">+</span> Tambah Transaksi
                    </a>
                    <a href="{{ route('reports.export-excel', request()->query()) }}" class="secondary-action">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3v12m0 0 4-4m-4 4-4-4M5 20h14" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Export
                    </a>
                    <button class="secondary-action col-span-2 sm:col-span-1">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 5h16l-6 7v5l-4 2v-7L4 5Z" stroke="currentColor" stroke-width="2"
                                stroke-linejoin="round" />
                        </svg>
                        Filter
                    </button>
                </div>
            </div>

            <div class="ff-filter-grid">
                <select name="type" class="form-control">
                    <option value="">Semua Tipe</option>
                    <option value="income" @selected(request('type') === 'income')>Pemasukan</option>
                    <option value="expense" @selected(request('type') === 'expense')>Pengeluaran</option>
                </select>
                <select name="category_id" class="form-control">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) request('category_id') === $category->id)>{{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
                <select name="payment_method" class="form-control">
                    <option value="">Semua Metode</option>
                    <option value="cash" @selected(request('payment_method') === 'cash')>Cash</option>
                    <option value="bank" @selected(request('payment_method') === 'bank')>Bank</option>
                    <option value="e-wallet" @selected(request('payment_method') === 'e-wallet')>E-Wallet</option>
                </select>
                <select name="wallet_id" class="form-control">
                    <option value="">Semua Dompet</option>
                    @foreach ($wallets as $wallet)
                        <option value="{{ $wallet->id }}" @selected((int) request('wallet_id') === $wallet->id)>{{ $wallet->wallet_name }}</option>
                    @endforeach
                </select>
                <div class="grid min-w-0 grid-cols-[minmax(0,1fr)_auto] gap-2">

                    @if (request()->hasAny([
                            'search',
                            'date_from',
                            'date_to',
                            'type',
                            'category_id',
                            'payment_method',
                            'wallet_id',
                            'status',
                        ]))
                        <a href="{{ route('transactions.index') }}" class="ff-icon-button" title="Reset filter"
                            aria-label="Reset filter">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 4v6h6M20 20v-6h-6M5.5 15a7 7 0 0 0 11.4 2.2L20 14M4 10l3.1-3.2A7 7 0 0 1 18.5 9"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <div class="grid min-w-0 gap-4 xl:grid-cols-[minmax(0,1fr)_190px]">
            <x-card class="overflow-hidden">
                <div class="ff-card-header">
                    <div>
                        <h2 class="section-heading">Daftar Transaksi</h2>
                        <p class="ff-muted mt-1">{{ $dateRange ? 'Periode ' . $dateRange : 'Semua transaksi terbaru' }}</p>
                    </div>
                    <span
                        class="rounded-full bg-slate-100 px-3 py-1.5 text-[10px] font-bold text-slate-600">{{ $transactions->total() }}
                        transaksi</span>
                </div>

                <div class="ff-table-wrap">
                    <table class="ff-table ff-compact-table min-w-[900px]">
                        <thead>
                            <tr>
                                <th>Kode Transaksi</th>
                                <th>Tanggal</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Tipe</th>
                                <th>Metode</th>
                                <th class="text-right">Nominal</th>
                                <th>Status</th>
                                <th>Pembuat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td class="whitespace-nowrap font-semibold text-slate-700">
                                        {{ $transaction->transaction_code }}</td>
                                    <td class="whitespace-nowrap">
                                        <b
                                            class="block text-slate-700">{{ $transaction->transaction_date?->translatedFormat('d M Y') }}</b>
                                        <span
                                            class="mt-1 block text-[10px] text-slate-400">{{ $transaction->created_at?->format('H:i') }}</span>
                                    </td>
                                    <td class="max-w-[180px]">
                                        <a href="{{ route('transactions.edit', $transaction) }}"
                                            class="block truncate font-bold text-slate-950">{{ $transaction->title }}</a>
                                        <span
                                            class="mt-1 block truncate text-[10px] text-slate-500">{{ $transaction->description ?: 'Tanpa deskripsi' }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg px-2.5 py-1.5 text-[10px] font-bold"
                                            style="color: {{ $transaction->category?->color ?? '#64748b' }}; background-color: {{ ($transaction->category?->color ?? '#64748b') . '14' }}">
                                            {{ $transaction->category?->category_name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap">
                                        {{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                                    <td class="whitespace-nowrap font-semibold">
                                        {{ $transaction->wallet?->wallet_name ?? strtoupper($transaction->payment_method) }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-right font-extrabold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $transaction->type === 'income' ? '' : '-' }}{{ $formatCurrency($transaction->amount) }}
                                    </td>
                                    <td>
                                        <x-badge
                                            tone="{{ $transaction->status === 'success' ? 'success' : 'cancel' }}">{{ $transaction->status === 'success' ? 'Sukses' : 'Batal' }}</x-badge>
                                    </td>
                                    <td class="whitespace-nowrap">{{ $transaction->user?->name ?? '-' }}</td>
                                    <td>
                                        <details class="group relative">
                                            <summary class="ff-icon-button mx-auto h-8 w-8 cursor-pointer list-none">⋮
                                            </summary>
                                            <div
                                                class="absolute right-0 z-20 mt-1 w-28 rounded-xl border border-slate-200 bg-white p-1 shadow-xl">
                                                <a href="{{ route('transactions.edit', $transaction) }}"
                                                    class="block rounded-lg px-3 py-2 text-[11px] font-bold hover:bg-slate-50">Edit</a>
                                                @if ($transaction->attachment)
                                                    <a href="{{ asset('storage/' . $transaction->attachment) }}"
                                                        target="_blank"
                                                        class="block rounded-lg px-3 py-2 text-[11px] font-bold hover:bg-slate-50">Lampiran</a>
                                                @endif
                                                <form method="POST"
                                                    action="{{ route('transactions.destroy', $transaction) }}"
                                                    onsubmit="return confirm('Hapus transaksi ini?')">
                                                    @csrf @method('DELETE')
                                                    <button
                                                        class="w-full rounded-lg px-3 py-2 text-left text-[11px] font-bold text-rose-600 hover:bg-rose-50">Hapus</button>
                                                </form>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">
                                        <div class="ff-empty">Belum ada transaksi yang cocok.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-4 py-3">{{ $transactions->links() }}</div>
            </x-card>

            <aside class="grid content-start gap-3 sm:grid-cols-3 xl:grid-cols-1">
                <x-card class="p-4 sm:col-span-3 xl:col-span-1">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xs font-extrabold">Ringkasan Transaksi</h2>
                        <span class="text-slate-400">ⓘ</span>
                    </div>
                </x-card>
                @foreach ([['Pemasukan', $transactionSummary['income']['amount'], $transactionSummary['income']['count'] . ' transaksi', 'icon-income.svg', 'bg-emerald-50'], ['Pengeluaran', $transactionSummary['expense']['amount'], $transactionSummary['expense']['count'] . ' transaksi', 'icon-expense.svg', 'bg-rose-50'], ['Transaksi Hari Ini', $transactionSummary['today']['amount'], $transactionSummary['today']['count'] . ' transaksi', 'icon-wallet.svg', 'bg-blue-50']] as [$label, $amount, $count, $icon, $bgClass])
                    <x-card class="p-4">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl {{ $bgClass }}">
                                <img src="{{ asset('assets/svg/' . $icon) }}" class="h-5 w-5" alt="">
                            </span>
                            <div class="min-w-0">
                                <span class="text-[10px] font-medium text-slate-500">{{ $label }}</span>
                                <strong
                                    class="mt-1 block break-words text-sm font-extrabold">{{ $formatCurrency($amount) }}</strong>
                            </div>
                        </div>
                        <p class="mt-3 text-[10px] font-medium text-slate-500">{{ $count }}</p>
                        <p class="mt-2 text-[9px] text-slate-400">{{ $transactionSummary['period'] }}</p>
                    </x-card>
                @endforeach
                <a href="{{ route('reports.history') }}" class="secondary-action sm:col-span-3 xl:col-span-1">Lihat
                    laporan lengkap →</a>
            </aside>
        </div>
    </div>
@endsection
