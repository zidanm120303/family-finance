@extends('layouts.app')
@php
    $isEdit = isset($transaction);
    $pageTitle = $isEdit ? 'Edit Transaksi' : 'Tambah Transaksi';
@endphp
@section('title',$pageTitle.' - FamFinance')
@section('page_title',$pageTitle)
@section('page_subtitle','Kelola pemasukan dan pengeluaran keluarga')
@section('content')
<form method="POST" action="{{ $isEdit ? route('transactions.update', $transaction) : route('transactions.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-6 xl:grid-cols-12">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <x-card class="p-6 xl:col-span-8">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-lg">Informasi Transaksi</h2>
                <p class="mt-1 text-sm text-slate-500">Status sukses akan langsung memengaruhi saldo dompet.</p>
            </div>
            <x-badge tone="{{ $isEdit ? 'blue' : 'success' }}">{{ $transactionCode ?? 'Baru' }}</x-badge>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <label class="text-sm font-extrabold text-slate-700">
                Kode Transaksi
                <input disabled value="{{ $transactionCode }}" class="form-field mt-2 bg-slate-50">
            </label>
            <label class="text-sm font-extrabold text-slate-700">
                Judul Transaksi
                <input name="title" value="{{ old('title', $transaction->title ?? '') }}" class="form-field mt-2" placeholder="Contoh: Gaji Bulanan">
            </label>
            <label class="text-sm font-extrabold text-slate-700">
                Tipe
                <select name="type" class="form-field mt-2">
                    <option value="income" @selected(old('type', $transaction->type ?? '') === 'income')>Pemasukan</option>
                    <option value="expense" @selected(old('type', $transaction->type ?? 'expense') === 'expense')>Pengeluaran</option>
                </select>
            </label>
            <label class="text-sm font-extrabold text-slate-700">
                Kategori
                <select name="category_id" class="form-field mt-2">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('category_id', $transaction->category_id ?? 0) === $category->id)>{{ ucfirst($category->type) }} - {{ $category->category_name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm font-extrabold text-slate-700">
                Nominal
                <input name="amount" type="number" min="1" value="{{ old('amount', isset($transaction) ? (int) $transaction->amount : '') }}" class="form-field mt-2" placeholder="150000">
            </label>
            <label class="text-sm font-extrabold text-slate-700">
                Tanggal
                <input name="transaction_date" type="date" value="{{ old('transaction_date', isset($transaction) ? $transaction->transaction_date->toDateString() : now()->toDateString()) }}" class="form-field mt-2">
            </label>
            <label class="text-sm font-extrabold text-slate-700">
                Metode
                <select name="payment_method" class="form-field mt-2">
                    <option value="cash" @selected(old('payment_method', $transaction->payment_method ?? '') === 'cash')>Cash</option>
                    <option value="e-wallet" @selected(old('payment_method', $transaction->payment_method ?? '') === 'e-wallet')>E-Wallet</option>
                    <option value="bank" @selected(old('payment_method', $transaction->payment_method ?? '') === 'bank')>Bank</option>
                </select>
            </label>
            <label class="text-sm font-extrabold text-slate-700">
                Dompet
                <select name="wallet_id" class="form-field mt-2">
                    <option value="">Tanpa dompet</option>
                    @foreach($wallets as $wallet)
                        <option value="{{ $wallet->id }}" @selected((int) old('wallet_id', $transaction->wallet_id ?? 0) === $wallet->id)>{{ $wallet->wallet_name }} - Rp {{ number_format($wallet->balance,0,',','.') }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm font-extrabold text-slate-700">
                Status
                <select name="status" class="form-field mt-2">
                    <option value="pending" @selected(old('status', $transaction->status ?? '') === 'pending')>Pending</option>
                    <option value="success" @selected(old('status', $transaction->status ?? 'success') === 'success')>Sukses</option>
                    <option value="cancel" @selected(old('status', $transaction->status ?? '') === 'cancel')>Batal</option>
                </select>
            </label>
            <label class="md:col-span-2 text-sm font-extrabold text-slate-700">
                Deskripsi
                <textarea name="description" class="form-field mt-2" rows="4">{{ old('description', $transaction->description ?? '') }}</textarea>
            </label>
            <div class="md:col-span-2 text-sm font-extrabold text-slate-700" x-data="{ fileName: '' }">
                <span>Upload Lampiran</span>
                <label class="ff-upload-box mt-2">
                    <span class="ff-upload-icon" aria-hidden="true">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none">
                            <path d="M12 16V5m0 0 4 4m-4-4-4 4" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5 15v2.5A2.5 2.5 0 0 0 7.5 20h9a2.5 2.5 0 0 0 2.5-2.5V15" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="text-sm font-extrabold text-slate-800">Pilih file bukti transaksi</span>
                    <span class="text-xs font-semibold text-slate-500" x-text="fileName || 'JPG, PNG, atau PDF maksimal 5MB'"></span>
                    <input name="attachment" type="file" accept=".jpg,.jpeg,.png,.pdf" class="sr-only" @change="fileName = $event.target.files[0]?.name || ''">
                </label>
                @if($isEdit && ! empty($transaction->attachment))
                    <a href="{{ asset('storage/'.$transaction->attachment) }}" target="_blank" class="mt-2 inline-flex text-xs font-extrabold text-emerald-700">Lampiran saat ini</a>
                @endif
            </div>
        </div>

        <div class="mt-6 flex flex-wrap justify-end gap-3">
            <a href="{{ route('transactions.index') }}" class="px-5 py-3 text-sm font-extrabold text-slate-600">Batal</a>
            <x-button type="submit">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Transaksi' }}</x-button>
        </div>
    </x-card>

    <div class="space-y-6 xl:col-span-4">
        <x-card class="p-6">
            <h2 class="font-extrabold text-lg">Ringkasan Dampak</h2>
            <p class="mt-3 text-sm leading-6 text-slate-500">Pemasukan sukses menambah saldo dompet. Pengeluaran sukses mengurangi saldo dompet. Status pending atau batal tidak mengubah saldo.</p>
            <img src="{{ asset('assets/illustration/budget-report-illustration.png') }}" class="mt-6 h-52 w-full object-contain" alt="Ilustrasi transaksi">
        </x-card>

        @if($isEdit)
            <x-card class="p-6">
                <h2 class="font-extrabold text-lg text-rose-700">Hapus Transaksi</h2>
                <p class="mt-2 text-sm text-slate-500">Saldo dompet akan dikembalikan jika transaksi ini berstatus sukses.</p>
                <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" class="mt-5">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger" class="w-full">Hapus</x-button>
                </form>
            </x-card>
        @endif
    </div>
</form>
@endsection
