@extends('layouts.app')
@section('title', 'Transaksi - FamFinance')
@section('page_title', 'Transaksi')
@section('page_subtitle', 'Filter, audit, dan kelola transaksi keluarga')
@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@section('content')
    <div class="transactions-page">
        <div class="transaction-toolbar">
        <div class="transaction-filter-area">
            <form id="transaction-filter-form" method="GET" class="transaction-filter-form">
                <div class="transaction-filter-top">
                    <label class="filter-control filter-search-control">
                        <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <input name="search" value="{{ request('search') }}" class="filter-control-input"
                            placeholder="Cari judul atau kode transaksi...">
                    </label>

                    <label class="filter-control filter-date-control date-range-picker">
                        <svg class="h-5 w-5 shrink-0 text-slate-700" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M8 2v4m8-4v4M3.5 9.5h17M6 5h12a2.5 2.5 0 0 1 2.5 2.5v11A2.5 2.5 0 0 1 18 21H6a2.5 2.5 0 0 1-2.5-2.5v-11A2.5 2.5 0 0 1 6 5Z"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <input id="transaction-date-range" name="date_range" value="{{ $dateRange }}"
                            class="filter-control-input date-range-input" placeholder="Pilih rentang tanggal"
                            autocomplete="off">
                        <svg class="h-5 w-5 shrink-0 text-slate-700" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M8 2v4m8-4v4M3.5 9.5h17M8 14h.01M12 14h.01M16 14h.01M8 17h.01M12 17h.01M16 17h.01"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </label>
                </div>

                <div class="transaction-filter-options">
                    <label class="filter-select-control">
                        <select name="type" class="filter-control-input">
                            <option value="">Semua Tipe</option>
                            <option value="income" @selected(request('type') === 'income')>Pemasukan</option>
                            <option value="expense" @selected(request('type') === 'expense')>Pengeluaran</option>
                        </select>
                        <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </label>

                    <label class="filter-select-control">
                        <select name="category_id" class="filter-control-input">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) request('category_id') === $category->id)>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </label>

                    <label class="filter-select-control">
                        <select name="payment_method" class="filter-control-input">
                            <option value="">Semua Metode</option>
                            <option value="cash" @selected(request('payment_method') === 'cash')>Cash</option>
                            <option value="e-wallet" @selected(request('payment_method') === 'e-wallet')>E-Wallet</option>
                            <option value="bank" @selected(request('payment_method') === 'bank')>Bank</option>
                        </select>
                        <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </label>

                    <label class="filter-select-control">
                        <select name="wallet_id" class="filter-control-input">
                            <option value="">Semua Dompet</option>
                            @foreach ($wallets as $wallet)
                                <option value="{{ $wallet->id }}" @selected((int) request('wallet_id') === $wallet->id)>
                                    {{ $wallet->wallet_name }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </label>

                    <label class="filter-select-control">
                        <select name="status" class="filter-control-input">
                            <option value="">Semua Status</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="success" @selected(request('status') === 'success')>Sukses</option>
                            <option value="cancel" @selected(request('status') === 'cancel')>Batal</option>
                        </select>
                        <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </label>
                </div>
            </form>
        </div>

        <div class="transaction-action-area">
            <div class="filter-actions">
                <a href="{{ route('transactions.create') }}" class="filter-button filter-button-primary">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" />
                    </svg>
                    <span>Tambah Transaksi</span>
                </a>
                <a href="{{ route('reports.export-excel', request()->query()) }}"
                    class="filter-button filter-button-secondary">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3v11m0 0 4-4m-4 4-4-4M5 17v2a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-2" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Export</span>
                </a>
                <button type="submit" form="transaction-filter-form" class="filter-button filter-button-secondary">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 5h16l-6.5 7.5V18l-3 1.5v-7L4 5Z" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Filter</span>
                </button>
            </div>
        </div>

        </div>

        <div class="transaction-content-layout">
        <div class="transactions-main">
            <x-card class="transactions-table-card transaction-table-card overflow-hidden p-3">
                <div class="ff-table-wrap ff-table-wrap-scroll">
                    <table class="ff-table ff-table-scroll transactions-table">
                        <colgroup>
                            <col class="trx-col-code">
                            <col class="trx-col-date">
                            <col class="trx-col-title">
                            <col class="trx-col-category">
                            <col class="trx-col-type">
                            <!-- <col class="trx-col-method"> -->
                            <col class="trx-col-money">
                            <col class="trx-col-status">
                            <col class="trx-col-user">
                            <col class="trx-col-action">
                        </colgroup>
                        <thead class="text-slate-700">
                            <tr>
                                <th class="ff-cell-code text-left">Kode Transaksi</th>
                                <th class="ff-cell-date text-left">
                                    <span class="inline-flex items-center gap-1">
                                        Tanggal
                                        <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none"
                                            aria-hidden="true">
                                            <path d="m8 10 4-4 4 4M16 14l-4 4-4-4" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </th>
                                <th class="ff-cell-title text-left">Judul</th>
                                <th class="ff-cell-category text-left">Kategori</th>
                                <th class="ff-cell-type text-left">Tipe</th>
                                <!-- <th class="ff-cell-method text-left">Metode</th> -->
                                <th class="ff-cell-money">Nominal</th>
                                <th class="ff-cell-status text-left">Status</th>
                                <th class="ff-cell-user text-left">Pembuat</th>
                                <th class="ff-cell-action text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                @php
                                    $typeLabel = $trx->type === 'income' ? 'Pemasukan' : 'Pengeluaran';
                                    $statusLabels = [
                                        'success' => 'Sukses',
                                        'pending' => 'Pending',
                                        'cancel' => 'Batal',
                                    ];
                                    $methodLabels = [
                                        'cash' => 'Cash',
                                        'e-wallet' => $trx->wallet?->wallet_name ?? 'E-Wallet',
                                        'bank' => $trx->wallet?->wallet_name ?? 'Bank',
                                    ];
                                    $categoryColor =
                                        $trx->category?->color ?: ($trx->type === 'income' ? '#10B981' : '#F43F5E');
                                    $categoryBg = preg_match('/^#[0-9A-Fa-f]{6}$/', $categoryColor)
                                        ? $categoryColor . '1A'
                                        : '#ECFDF5';
                                    $categoryIcon =
                                        $trx->category?->icon ?:
                                        ($trx->type === 'income'
                                            ? 'icon-income.svg'
                                            : 'icon-expense.svg');
                                    $methodLabel = $methodLabels[$trx->payment_method] ?? ucfirst($trx->payment_method);
                                    $methodTone =
                                        $trx->payment_method === 'cash'
                                            ? 'cash'
                                            : ($trx->payment_method === 'e-wallet'
                                                ? 'wallet'
                                                : 'bank');
                                    $methodMark =
                                        $trx->payment_method === 'cash' ? 'Rp' : strtoupper(substr($methodLabel, 0, 3));
                                @endphp
                                <tr class="border-t border-slate-100">
                                    <td data-label="Kode" class="ff-cell-code font-bold text-slate-700">
                                        {{ $trx->transaction_code }}</td>
                                    <td data-label="Tanggal" class="ff-cell-date text-slate-500">
                                        <div class="font-semibold text-slate-700">
                                            {{ $trx->transaction_date->translatedFormat('d M Y') }}</div>
                                        <div class="mt-0.5 text-xs font-medium text-slate-500">
                                            {{ $trx->created_at?->format('H:i') ?? '--:--' }}</div>
                                    </td>
                                    <td data-label="Judul" class="ff-cell-title ff-table-primary leading-snug">
                                        <div class="font-extrabold text-slate-800">{{ $trx->title }}</div>
                                        <div class="mt-1 line-clamp-2 text-xs font-semibold text-slate-500">
                                            {{ $trx->description ?: '-' }}</div>
                                    </td>
                                    <td data-label="Kategori" class="ff-cell-category">
                                        <span class="trx-category-pill"
                                            style="--category-color: {{ $categoryColor }}; --category-bg: {{ $categoryBg }};">
                                            <span class="trx-category-icon">
                                                <img src="{{ asset('assets/svg/' . $categoryIcon) }}" alt="">
                                            </span>
                                            <span>{{ $trx->category->category_name ?? 'Lainnya' }}</span>
                                        </span>
                                    </td>
                                    <td data-label="Tipe" class="ff-cell-type font-semibold text-slate-700">
                                        {{ $typeLabel }}
                                    </td>
                                    {{-- <td data-label="Metode" class="ff-cell-method">
                                        <span class="trx-method">
                                            <span class="trx-method-mark trx-method-{{ $methodTone }}">
                                                {{ $methodMark }}
                                            </span>
                                            <span>{{ $methodLabel }}</span>
                                        </span>
                                    </td> --}}
                                    <td data-label="Nominal"
                                        class="ff-cell-money font-semibold {{ $trx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $trx->type === 'expense' ? '-Rp' : 'Rp' }}
                                        {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                    <td data-label="Status" class="ff-cell-status">
                                        <span class="trx-status trx-status-{{ $trx->status }}">
                                            {{ $statusLabels[$trx->status] ?? ucfirst($trx->status) }}
                                        </span>
                                    </td>
                                    <td data-label="Pembuat" class="ff-cell-user font-semibold text-slate-700">
                                        {{ $trx->user->name ?? '-' }}</td>
                                    <td data-label="Aksi" class="ff-cell-action text-right">
                                        <a href="{{ route('transactions.edit', $trx) }}" class="trx-action-button"
                                            aria-label="Edit transaksi {{ $trx->transaction_code }}">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M12 6.75h.01M12 12h.01M12 17.25h.01" stroke="currentColor"
                                                    stroke-width="3" stroke-linecap="round" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="ff-table-empty p-8 text-center text-slate-500">Belum ada
                                        transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-2 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm font-semibold text-slate-500">
                            @if ($transactions->total() > 0)
                                Menampilkan {{ $transactions->firstItem() }}-{{ $transactions->lastItem() }} dari
                                {{ $transactions->total() }} transaksi
                            @else
                                Belum ada transaksi
                            @endif
                        </p>
                        @if ($transactions->hasPages())
                            <div class="ff-pagination">
                                {{ $transactions->onEachSide(1)->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </x-card>
        </div>

        <aside class="transaction-summary-panel space-y-4">
            <x-card class="p-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="font-extrabold text-slate-900">Ringkasan Transaksi</h2>
                    <span
                        class="grid h-6 w-6 place-items-center rounded-full border border-slate-200 text-xs font-extrabold text-slate-400">i</span>
                </div>

                <div class="mt-4 space-y-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm">
                        <div class="flex items-start gap-3">
                            <span
                                class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-emerald-50 text-emerald-700">
                                <img src="{{ asset('assets/svg/icon-income.svg') }}" class="h-5 w-5" alt="">
                            </span>
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold text-slate-500">Pemasukan</div>
                                <div class="mt-1 text-base font-semibold leading-tight text-slate-650">
                                    Rp {{ number_format($transactionSummary['income']['amount'], 0, ',', '.') }}
                                </div>
                                <div class="mt-1 text-xs font-semibold text-slate-500">
                                    {{ $transactionSummary['income']['count'] }} transaksi
                                </div>
                                <div class="mt-3 text-xs font-semibold text-slate-500">
                                    dari {{ $transactionSummary['period'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm">
                        <div class="flex items-start gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-rose-50 text-rose-700">
                                <img src="{{ asset('assets/svg/icon-expense.svg') }}" class="h-5 w-5" alt="">
                            </span>
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold text-slate-500">Pengeluaran</div>
                                <div class="mt-1 text-base font-semibold leading-tight text-slate-650">
                                    Rp {{ number_format($transactionSummary['expense']['amount'], 0, ',', '.') }}
                                </div>
                                <div class="mt-1 text-xs font-semibold text-slate-500">
                                    {{ $transactionSummary['expense']['count'] }} transaksi
                                </div>
                                <div class="mt-3 text-xs font-semibold text-slate-500">
                                    dari {{ $transactionSummary['period'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm">
                        <div class="flex items-start gap-3">
                            <span
                                class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-emerald-50 text-emerald-700">
                                <img src="{{ asset('assets/svg/icon-budget.svg') }}" class="h-5 w-5" alt="">
                            </span>
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold text-slate-500">Transaksi Hari Ini</div>
                                <div class="mt-1 text-base font-semibold leading-tight text-slate-650">
                                    Rp {{ number_format($transactionSummary['today']['amount'], 0, ',', '.') }}
                                </div>
                                <div class="mt-1 text-xs font-semibold text-slate-500">
                                    {{ $transactionSummary['today']['count'] }} transaksi sukses
                                </div>
                                <div class="mt-3 text-xs font-semibold text-slate-500">
                                    hari ini, {{ $transactionSummary['today']['date'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('reports.history') }}"
                    class="mt-4 flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-extrabold text-emerald-700">
                    Lihat laporan lengkap
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
            </x-card>
            {{-- <x-card class="p-6">
                <img src="{{ asset('assets/illustration/budget-report-illustration.png') }}"
                    class="h-52 w-full object-contain" alt="Ilustrasi laporan">
                <p class="mt-4 text-sm leading-6 text-slate-500">Riwayat perubahan transaksi tersimpan otomatis saat
                    create,
                    update, dan delete.</p>
            </x-card> --}}
        </aside>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('transaction-date-range');

            if (!input || !window.flatpickr) {
                return;
            }

            window.flatpickr(input, {
                mode: 'range',
                dateFormat: 'd-m-Y',
                altInput: true,
                altFormat: 'd F Y',
                allowInput: true,
                minDate: null,
                maxDate: null,
                locale: {
                    firstDayOfWeek: 1,
                    rangeSeparator: ' - ',
                    weekdays: {
                        shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                        longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                    },
                    months: {
                        shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt',
                            'Nov',
                            'Des'
                        ],
                        longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                            'Agustus',
                            'September', 'Oktober', 'November', 'Desember'
                        ],
                    },
                },
            });
        });
    </script>
@endpush
