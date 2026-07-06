@extends('layouts.app')
@section('title', 'Tambah Transaksi - FamFinance')
@section('page_title', 'Tambah Transaksi')
@section('page_subtitle', 'Dashboard > Transaksi > Tambah Transaksi')
@section('content')
    <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data"
        class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        @csrf
        <x-card class="p-6 xl:col-span-9">
            <h2 class="font-extrabold text-lg mb-6">Informasi Transaksi</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5"><label>Kode Transaksi<input disabled
                        value="{{ $transactionCode ?? 'TRX-20240531-001' }}"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 bg-slate-50"></label><label>Judul
                    Transaksi<input name="title" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3"
                        placeholder="Contoh: Gaji Bulanan"></label><label>Tipe<select name="type"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select></label><label>Kategori<select name="category_id"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                        @foreach ($categories ?? [] as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </label><label>Nominal<input name="amount" type="number"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3"
                        placeholder="150000"></label><label>Tanggal<input name="transaction_date" type="date"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3"></label><label>Metode<select
                        name="payment_method" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                        <option value="cash">Cash</option>
                        <option value="e-wallet">E-Wallet</option>
                        <option value="bank">Bank</option>
                    </select></label><label>Dompet<select name="wallet_id"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                        @foreach ($wallets ?? [] as $wallet)
                            <option value="{{ $wallet->id }}">{{ $wallet->wallet_name }}</option>
                        @endforeach
                </label><label class="md:col-span-2">Deskripsi
                    <textarea name="description" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" rows="4"></textarea>
                </label><label class="md:col-span-2">Upload Lampiran<input name="attachment" type="file"
                        class="mt-2 w-full rounded-2xl border border-dashed border-slate-300 px-4 py-8"></label></div>
            <div class="flex justify-end gap-3 mt-6"><a href="{{ route('transactions.index') }}"
                    class="px-5 py-3 font-bold">Batal</a><button
                    class="rounded-2xl bg-emerald-600 px-6 py-3 text-white font-bold">Simpan Transaksi</button></div>
        </x-card>
        <x-card class="p-6 xl:col-span-3">
            <h2 class="font-extrabold text-lg mb-4">Ringkasan</h2>
            <p class="text-slate-600 text-sm">Pemasukan akan menambah saldo, pengeluaran akan mengurangi saldo jika status
                sukses.</p>
        </x-card>
    </form>
@endsection
