@extends('layouts.app')

@section('title', 'Pengaturan - FamFinance')
@section('page_title', 'Pengaturan')
@section('page_subtitle', 'Informasi akun dan profil keluarga')

@section('content')
    @php
        $user = auth()->user();
        $family = $user?->family;
    @endphp

    <div class="page-stack">
        <section class="grid gap-6 xl:grid-cols-12">
            <x-card class="p-5 sm:p-6 xl:col-span-7">
                <div class="flex items-start gap-4">
                    <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-emerald-100 text-lg font-extrabold text-emerald-700">
                        {{ str($family?->family_name ?? 'FF')->substr(0, 2)->upper() }}
                    </span>
                    <div class="min-w-0">
                        <h2 class="section-heading">Profil Keluarga</h2>
                        <p class="mt-1 text-sm font-semibold text-slate-500">Identitas utama yang dipakai bersama oleh seluruh anggota.</p>
                    </div>
                </div>

                <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                    @foreach([
                        ['Nama Keluarga', $family?->family_name],
                        ['Kode Keluarga', $family?->family_code],
                        ['Kota & Provinsi', collect([$family?->city, $family?->province])->filter()->join(', ')],
                        ['Kode Pos', $family?->postal_code],
                        ['Telepon', $family?->phone],
                        ['Jumlah Anggota', ($family?->users()->count() ?? 0).' orang'],
                    ] as [$label, $value])
                        <div class="min-w-0 rounded-2xl bg-slate-50 p-4">
                            <dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">{{ $label }}</dt>
                            <dd class="mt-2 break-words font-extrabold text-slate-950">{{ $value ?: '-' }}</dd>
                        </div>
                    @endforeach
                    <div class="min-w-0 rounded-2xl bg-slate-50 p-4 sm:col-span-2">
                        <dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Alamat</dt>
                        <dd class="mt-2 break-words font-extrabold leading-6 text-slate-950">{{ $family?->address ?: '-' }}</dd>
                    </div>
                </dl>
            </x-card>

            <div class="grid gap-6 xl:col-span-5">
                <x-card class="p-5 sm:p-6">
                    <h2 class="section-heading">Akun Anda</h2>
                    <div class="mt-5 flex min-w-0 items-center gap-4 rounded-2xl bg-emerald-50 p-4">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-white font-extrabold text-emerald-700">
                            {{ str($user?->name ?? 'FF')->substr(0, 2)->upper() }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate font-extrabold text-slate-950">{{ $user?->name }}</p>
                            <p class="mt-1 truncate text-sm font-semibold text-slate-500">{{ $user?->email }}</p>
                        </div>
                    </div>
                    <dl class="mt-4 grid gap-3">
                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 px-4 py-3">
                            <dt class="text-sm font-bold text-slate-500">Role</dt>
                            <dd class="text-right text-sm font-extrabold text-slate-950">{{ $user?->role?->role_name ?? '-' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 px-4 py-3">
                            <dt class="text-sm font-bold text-slate-500">Status</dt>
                            <dd class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-700">Aktif</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 px-4 py-3">
                            <dt class="text-sm font-bold text-slate-500">Login terakhir</dt>
                            <dd class="text-right text-sm font-extrabold text-slate-950">{{ $user?->last_login?->translatedFormat('d M Y, H:i') ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-card>

                <x-card class="overflow-hidden">
                    <div class="grid items-center gap-4 bg-blue-50 p-5 sm:grid-cols-[1fr_9rem] sm:p-6">
                        <div>
                            <h2 class="section-heading">Data Terlindungi</h2>
                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">
                                Setiap transaksi terhubung ke keluarga dan pengguna yang mencatatnya.
                            </p>
                        </div>
                        <img src="{{ asset('assets/illustration/family-finance-security-illustration.png') }}" class="mx-auto h-32 w-full object-contain" alt="Ilustrasi keamanan keluarga">
                    </div>
                </x-card>
            </div>
        </section>
    </div>
@endsection
