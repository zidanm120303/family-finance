@extends('layouts.app')

@php
    $isEdit = isset($transaction);
    $pageTitle = $isEdit ? 'Edit Transaksi' : 'Tambah Transaksi';
    $formatCurrency = fn ($value) => 'Rp '.number_format((float) $value, 0, ',', '.');
    $fieldClass = 'form-control mt-2';
    $labelClass = 'block min-w-0 text-xs font-bold text-slate-700';
    $transactionDate = old('transaction_date', $isEdit ? \Illuminate\Support\Carbon::parse($transaction->transaction_date)->toDateString() : now()->toDateString());
    $initialType = old('type', $transaction->type ?? 'income');
    $initialMethod = old('payment_method', $transaction->payment_method ?? 'cash');
    $initialStatus = old('status', $transaction->status ?? 'success');
    $initialAmount = old('amount', $isEdit ? (int) $transaction->amount : 0);
@endphp

@section('title', $pageTitle.' - FamFinance')
@section('page_title', $pageTitle)
@section('page_subtitle', 'Transaksi › '.$pageTitle)

@section('content')
    <form method="POST" action="{{ $isEdit ? route('transactions.update', $transaction) : route('transactions.store') }}"
        enctype="multipart/form-data"
        class="grid min-w-0 items-start gap-5 xl:grid-cols-[minmax(0,1fr)_292px]"
        x-data="{
            type: @js($initialType),
            method: @js($initialMethod),
            status: @js($initialStatus),
            amount: Number(@js($initialAmount)) || 0,
            wallet: @js(old('wallet_id', $transaction->wallet_id ?? '')),
            fileName: '',
            formatAmount() { return new Intl.NumberFormat('id-ID').format(this.amount || 0) }
        }">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <x-card class="p-4 sm:p-5">
            <div class="mb-5 flex items-center gap-3 border-b border-slate-100 pb-4">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-emerald-50">
                    <img src="{{ asset('assets/svg/icon-wallet.svg') }}" class="h-5 w-5" alt="">
                </span>
                <div class="min-w-0">
                    <h2 class="text-sm font-extrabold">Informasi Transaksi</h2>
                    <p class="mt-1 text-[10px] font-medium text-slate-500">Status sukses akan memengaruhi saldo dompet.</p>
                </div>
            </div>

            <div class="grid min-w-0 gap-x-5 gap-y-4 md:grid-cols-2">
                <label class="{{ $labelClass }}">Kode Transaksi
                    <input disabled value="{{ $transactionCode }}" class="{{ $fieldClass }} bg-slate-50 text-slate-500">
                    <span class="mt-1.5 block text-[9px] font-medium text-slate-400">Otomatis terisi</span>
                </label>
                <label class="{{ $labelClass }}">Judul Transaksi <span class="text-rose-500">*</span>
                    <input name="title" maxlength="120" value="{{ old('title', $transaction->title ?? '') }}" class="{{ $fieldClass }}" placeholder="Contoh: Gaji Bulanan, Belanja Mingguan" required>
                </label>

                <fieldset class="min-w-0">
                    <legend class="text-xs font-bold text-slate-700">Tipe Transaksi <span class="text-rose-500">*</span></legend>
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        @foreach([['income','Pemasukan','↑'],['expense','Pengeluaran','↓']] as [$value,$label,$symbol])
                            <label class="flex h-11 cursor-pointer items-center justify-center gap-2 rounded-xl border text-xs font-bold transition"
                                :class="type === '{{ $value }}' ? '{{ $value === 'income' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-rose-500 bg-rose-50 text-rose-700' }}' : 'border-slate-200 bg-white text-slate-600'">
                                <input name="type" value="{{ $value }}" type="radio" class="sr-only" x-model="type">
                                <span class="text-xl leading-none">{{ $symbol }}</span>{{ $label }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <label class="{{ $labelClass }}">Kategori <span class="text-rose-500">*</span>
                    <select name="category_id" class="{{ $fieldClass }}" required>
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $transaction->category_id ?? 0) === $category->id)>
                                {{ $category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }} — {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="{{ $labelClass }}">Nominal <span class="text-rose-500">*</span>
                    <span class="mt-2 flex h-11 items-center rounded-xl border border-slate-200 bg-white focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-100">
                        <span class="flex h-full items-center border-r border-slate-200 px-3 text-xs font-bold text-slate-500">Rp</span>
                        <input name="amount" type="number" min="1" x-model.number="amount" class="min-w-0 flex-1 border-0 bg-transparent px-3 text-[13px] font-semibold outline-none" placeholder="Masukkan nominal" required>
                    </span>
                    <span class="mt-1.5 block text-[9px] font-medium text-slate-400">Masukkan angka tanpa titik dan koma</span>
                </label>

                <label class="{{ $labelClass }}">Tanggal Transaksi <span class="text-rose-500">*</span>
                    <input name="transaction_date" type="date" value="{{ $transactionDate }}" class="{{ $fieldClass }}" required>
                </label>

                <fieldset class="min-w-0">
                    <legend class="text-xs font-bold text-slate-700">Metode Pembayaran <span class="text-rose-500">*</span></legend>
                    <div class="mt-2 grid grid-cols-3 gap-2">
                        @foreach([['cash','Cash'],['e-wallet','E-Wallet'],['bank','Bank']] as [$value,$label])
                            <label class="flex h-11 cursor-pointer items-center justify-center rounded-xl border px-2 text-[11px] font-bold transition"
                                :class="method === '{{ $value }}' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-white text-slate-600'">
                                <input name="payment_method" value="{{ $value }}" type="radio" class="sr-only" x-model="method">{{ $label }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <label class="{{ $labelClass }}">Dompet / Rekening
                    <select name="wallet_id" class="{{ $fieldClass }}" x-model="wallet">
                        <option value="">Tanpa dompet</option>
                        @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}">{{ $wallet->wallet_name }} — {{ $formatCurrency($wallet->balance) }}</option>
                        @endforeach
                    </select>
                </label>

                <fieldset class="min-w-0">
                    <legend class="text-xs font-bold text-slate-700">Status <span class="text-rose-500">*</span></legend>
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        @foreach([['success','Sukses'],['cancel','Batal']] as [$value,$label])
                            <label class="flex h-11 cursor-pointer items-center justify-center gap-2 rounded-xl border text-xs font-bold transition"
                                :class="status === '{{ $value }}' ? '{{ $value === 'success' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-rose-500 bg-rose-50 text-rose-700' }}' : 'border-slate-200 bg-white text-slate-600'">
                                <input name="status" value="{{ $value }}" type="radio" class="sr-only" x-model="status">
                                <span>{{ $value === 'success' ? '✓' : '×' }}</span>{{ $label }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <label class="{{ $labelClass }} md:col-span-2">Deskripsi / Catatan
                    <textarea name="description" rows="3" maxlength="500" class="form-control mt-2 h-20 py-3" placeholder="Tulis deskripsi atau catatan tambahan (opsional)">{{ old('description', $transaction->description ?? '') }}</textarea>
                </label>

                <div class="{{ $labelClass }} md:col-span-2">
                    Upload Lampiran / Bukti Transfer <span class="font-medium text-slate-400">(opsional)</span>
                    <div class="mt-2 grid min-w-0 gap-3" :class="fileName || @js($isEdit && !empty($transaction->attachment)) ? 'lg:grid-cols-[minmax(0,1fr)_270px]' : 'grid-cols-1'">
                        <label class="flex min-h-28 cursor-pointer items-center justify-center gap-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-center transition hover:border-emerald-500 hover:bg-emerald-50">
                            <span class="text-3xl text-emerald-600">☁</span>
                            <span class="text-left">
                                <b class="block text-[11px] text-slate-700">Seret &amp; lepas file di sini</b>
                                <span class="mt-1 block text-[10px] font-semibold text-emerald-600">atau klik untuk memilih file</span>
                                <span class="mt-1 block text-[9px] font-medium text-slate-400">Maks. 5MB. Format: JPG, PNG, PDF</span>
                            </span>
                            <input name="attachment" type="file" accept=".jpg,.jpeg,.png,.pdf" class="sr-only" @change="fileName = $event.target.files[0]?.name || ''">
                        </label>
                        <div class="flex min-h-28 items-center gap-3 rounded-xl border border-slate-200 p-3" x-show="fileName || @js($isEdit && !empty($transaction->attachment))">
                            <span class="grid h-16 w-16 shrink-0 place-items-center rounded-lg bg-emerald-50 text-2xl">📎</span>
                            <div class="min-w-0">
                                <b class="block truncate text-[11px]" x-text="fileName || @js($isEdit ? basename($transaction->attachment ?? '') : '')"></b>
                                @if($isEdit && !empty($transaction->attachment))
                                    <a href="{{ asset('storage/'.$transaction->attachment) }}" target="_blank" class="mt-2 block text-[10px] font-bold text-emerald-600">Lihat lampiran saat ini</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                <a href="{{ route('transactions.index') }}" class="secondary-action">Batal</a>
                <button class="primary-action">✓ {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Transaksi' }}</button>
            </div>
        </x-card>

        <aside class="grid content-start gap-4 xl:sticky xl:top-[108px]">
            <x-card class="p-5">
                <div class="flex items-center gap-3">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-50"><img src="{{ asset('assets/svg/icon-family.svg') }}" class="h-5 w-5" alt=""></span>
                    <h2 class="text-sm font-extrabold">Ringkasan</h2>
                </div>
                <div class="mt-5">
                    <span class="text-[10px] font-bold text-slate-500">Keluarga</span>
                    <div class="mt-2 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-50"><img src="{{ asset('assets/svg/icon-family.svg') }}" class="h-5 w-5" alt=""></span>
                        <div><b class="block text-xs">{{ auth()->user()?->family?->family_name }}</b><span class="text-[10px] text-slate-500">{{ auth()->user()?->family?->users()->count() }} anggota</span></div>
                    </div>
                </div>
                <div class="mt-4 border-t border-slate-100 pt-4">
                    <span class="text-[10px] font-bold text-slate-500">Dampak ke Saldo</span>
                    <strong class="mt-3 block text-xl font-extrabold" :class="type === 'income' ? 'text-emerald-600' : 'text-rose-600'">
                        <span x-text="type === 'income' ? '+' : '-'"></span> Rp <span x-text="formatAmount()"></span>
                    </strong>
                    <span class="mt-2 inline-block rounded-lg px-2 py-1 text-[9px] font-semibold" :class="status === 'cancel' ? 'bg-slate-100 text-slate-500' : (type === 'income' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700')" x-text="status === 'cancel' ? 'Transaksi batal tidak mengubah saldo' : (type === 'income' ? 'Pemasukan akan menambah saldo' : 'Pengeluaran akan mengurangi saldo')"></span>
                </div>
            </x-card>

            <x-card class="p-5">
                <div class="flex items-center gap-3"><span class="grid h-8 w-8 place-items-center rounded-full bg-amber-50">💡</span><h2 class="text-sm font-extrabold">Tips</h2></div>
                <ul class="mt-4 grid gap-4 text-[10px] font-medium leading-5 text-slate-500">
                    <li>♡ Pastikan kategori sesuai agar laporan keuangan akurat.</li>
                    <li>◎ Gunakan deskripsi untuk mencatat detail penting.</li>
                    <li>♢ Lampirkan bukti transaksi untuk dokumentasi lebih baik.</li>
                    <li>✓ Pilih Batal jika transaksi tidak jadi dilakukan.</li>
                </ul>
            </x-card>

            @if($isEdit)
                <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Hapus transaksi ini?')">
                    @csrf @method('DELETE')
                    <button class="danger-action w-full">Hapus Transaksi</button>
                </form>
            @endif
        </aside>
    </form>
@endsection
