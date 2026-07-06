@extends('layouts.app')

@php
    $icons = [
        'icon-wallet.svg' => 'Dompet',
        'icon-income.svg' => 'Pemasukan',
        'icon-expense.svg' => 'Pengeluaran',
        'icon-budget.svg' => 'Anggaran',
        'icon-category-health.svg' => 'Kesehatan',
        'icon-lightning.svg' => 'Listrik',
        'icon-wifi.svg' => 'Internet',
        'icon-shield.svg' => 'Proteksi',
        'icon-family.svg' => 'Keluarga',
    ];
    $fieldClass = 'form-control mt-2';
    $labelClass = 'block min-w-0 text-xs font-bold text-slate-700';
@endphp

@section('title', 'Kategori - FamFinance')
@section('page_title', 'Kategori')
@section('page_subtitle', 'Kategori')

@section('content')
    <div class="page-stack" x-data="{ tab: @js(request('type', 'expense')), addOpen: false }">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 overflow-x-auto rounded-xl border border-slate-200 bg-white p-1">
                <button type="button" @click="tab = 'expense'"
                    class="h-9 min-w-40 rounded-lg px-4 text-xs font-bold transition"
                    :class="tab === 'expense' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50'">
                    Kategori Pengeluaran <span class="ml-1 opacity-75">({{ $expenseCategories->count() }})</span>
                </button>
                <button type="button" @click="tab = 'income'"
                    class="h-9 min-w-40 rounded-lg px-4 text-xs font-bold transition"
                    :class="tab === 'income' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50'">
                    Kategori Pemasukan <span class="ml-1 opacity-75">({{ $incomeCategories->count() }})</span>
                </button>
            </div>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('categories.import-default') }}">
                    @csrf
                    <button class="secondary-action">Import Default</button>
                </form>
                <button type="button" @click="addOpen = true" class="primary-action">+ Tambah Kategori</button>
            </div>
        </div>

        <section class="grid min-w-0 items-start gap-4 xl:grid-cols-[minmax(0,1fr)_310px]">
            @foreach ([['type' => 'expense', 'title' => 'Kategori Pengeluaran', 'items' => $expenseCategories, 'description' => 'Kelompokkan seluruh biaya keluarga agar laporan lebih mudah dibaca.'], ['type' => 'income', 'title' => 'Kategori Pemasukan', 'items' => $incomeCategories, 'description' => 'Kelompokkan seluruh sumber pendapatan keluarga.']] as $group)
                <x-card class="overflow-hidden" x-show="tab === '{{ $group['type'] }}'" x-cloak>
                    <div class="ff-card-header">
                        <div>
                            <h2 class="section-heading">{{ $group['title'] }}</h2>
                            <p class="ff-muted mt-1">{{ $group['description'] }}</p>
                        </div>
                        <span
                            class="rounded-full bg-slate-100 px-3 py-1.5 text-[10px] font-bold text-slate-600">{{ $group['items']->count() }}
                            kategori</span>
                    </div>
                    <div class="ff-table-wrap">
                        <table class="ff-table min-w-[760px]">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th class="text-center">Transaksi</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($group['items'] as $category)
                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <div>
                                                    <b
                                                        class="block whitespace-nowrap text-slate-950">{{ $category->category_name }}</b>

                                                </div>
                                            </div>
                                        </td>
                                        <td class="max-w-[260px]">
                                            <p class="line-clamp-2 text-[11px] leading-5">
                                                {{ $category->description ?: 'Tanpa deskripsi' }}</p>
                                        </td>
                                        <td class="text-center font-bold">{{ $category->transactions_count }}</td>
                                        <td><x-badge
                                                tone="{{ $category->type === 'income' ? 'income' : 'expense' }}">{{ $category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</x-badge>
                                        </td>
                                        <td><span
                                                class="inline-flex items-center gap-2 text-[10px] font-bold text-emerald-700"><i
                                                    class="h-2 w-2 rounded-full bg-emerald-500"></i>Aktif</span></td>
                                        <td>
                                            <details class="group relative">
                                                <summary class="ff-icon-button mx-auto h-8 w-8 cursor-pointer list-none">⋮
                                                </summary>
                                                <div
                                                    class="absolute right-0 z-20 mt-1 w-64 rounded-xl border border-slate-200 bg-white p-3 text-left shadow-xl">
                                                    <form method="POST"
                                                        action="{{ route('categories.update', $category) }}"
                                                        class="grid gap-2">
                                                        @csrf @method('PUT')
                                                        <input name="category_name" value="{{ $category->category_name }}"
                                                            class="form-control" required>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <select name="type" class="form-control">
                                                                <option value="expense" @selected($category->type === 'expense')>
                                                                    Pengeluaran</option>
                                                                <option value="income" @selected($category->type === 'income')>
                                                                    Pemasukan</option>
                                                            </select>
                                                            <input name="color" type="color"
                                                                value="{{ $category->color ?: '#10B981' }}"
                                                                class="h-11 w-full rounded-xl border border-slate-200 p-1">
                                                        </div>
                                                        <select name="icon" class="form-control">
                                                            @foreach ($icons as $icon => $label)
                                                                <option value="{{ $icon }}"
                                                                    @selected(($category->icon ?: 'icon-wallet.svg') === $icon)>{{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <textarea name="description" rows="2" class="form-control h-16 py-2">{{ $category->description }}</textarea>
                                                        <button class="primary-action w-full">Simpan Perubahan</button>
                                                    </form>
                                                    <form method="POST"
                                                        action="{{ route('categories.destroy', $category) }}"
                                                        class="mt-2" onsubmit="return confirm('Hapus kategori ini?')">
                                                        @csrf @method('DELETE')
                                                        <button class="danger-action w-full"
                                                            @disabled($category->is_default || $category->transactions_count > 0)>Hapus</button>
                                                    </form>
                                                </div>
                                            </details>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="ff-empty">Belum ada kategori.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            @endforeach

            <x-card class="hidden p-5 xl:block">
                <div class="flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50"><img
                            src="{{ asset('assets/svg/icon-category-health.svg') }}" class="h-5 w-5" alt=""></span>
                    <div>
                        <h2 class="text-sm font-extrabold">Tambah Kategori</h2>
                        <p class="mt-1 text-[10px] text-slate-500">Buat kategori keluarga baru</p>
                    </div>
                </div>
                @include('pages.categories.partials.create-form', [
                    'fieldClass' => $fieldClass,
                    'labelClass' => $labelClass,
                    'icons' => $icons,
                ])
            </x-card>
        </section>

        <div x-show="addOpen" x-cloak>
            <button type="button" class="fixed inset-0 z-40 bg-slate-950/40" @click="addOpen = false"
                aria-label="Tutup panel"></button>
            <aside class="ff-drawer p-5" x-transition>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="section-heading">Tambah Kategori</h2>
                        <p class="ff-muted mt-1">Buat kategori keluarga baru.</p>
                    </div>
                    <button type="button" class="ff-icon-button" @click="addOpen = false">×</button>
                </div>
                @include('pages.categories.partials.create-form', [
                    'fieldClass' => $fieldClass,
                    'labelClass' => $labelClass,
                    'icons' => $icons,
                ])
            </aside>
        </div>
    </div>
@endsection
