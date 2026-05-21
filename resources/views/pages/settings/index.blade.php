@extends('layouts.app')
@section('page_title','Pengaturan')
@section('page_subtitle','Pengaturan minimal keluarga dan aplikasi')
@section('content')
<div class="grid gap-6 xl:grid-cols-12">
    <x-card class="p-6 xl:col-span-7">
        <h2 class="font-extrabold text-lg">Profil Keluarga</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <div class="rounded-2xl bg-slate-50 p-4"><div class="text-sm text-slate-500">Keluarga</div><div class="mt-1 font-extrabold">{{ auth()->user()->family?->family_name }}</div></div>
            <div class="rounded-2xl bg-slate-50 p-4"><div class="text-sm text-slate-500">Kode</div><div class="mt-1 font-extrabold">{{ auth()->user()->family?->family_code }}</div></div>
            <div class="rounded-2xl bg-slate-50 p-4"><div class="text-sm text-slate-500">Kota</div><div class="mt-1 font-extrabold">{{ auth()->user()->family?->city }}</div></div>
            <div class="rounded-2xl bg-slate-50 p-4"><div class="text-sm text-slate-500">Role Anda</div><div class="mt-1 font-extrabold">{{ auth()->user()->role?->role_name }}</div></div>
        </div>
    </x-card>
    <x-card class="p-6 xl:col-span-5">
        <img src="{{ asset('assets/illustration/family-finance-security-illustration.png') }}" class="h-64 w-full object-contain" alt="Ilustrasi pengaturan">
        <p class="mt-4 text-sm leading-6 text-slate-500">Pengaturan lanjutan seperti profil keluarga, preferensi mata uang, dan backup bisa ditambahkan pada phase berikutnya.</p>
    </x-card>
</div>
@endsection
