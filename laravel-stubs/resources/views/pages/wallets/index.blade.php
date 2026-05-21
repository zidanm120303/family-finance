@extends('layouts.app')
@section('page_title','Dompet')
@section('page_subtitle','Kelola semua dompet dan rekening keluarga Anda')
@section('content')
<div class="grid md:grid-cols-4 gap-5 mb-6">@foreach(['Total Saldo'=>'Rp 24.580.000','Jumlah Dompet'=>'5','Saldo Bank'=>'Rp 16.830.000','Saldo E-Wallet'=>'Rp 7.750.000'] as $k=>$v)<x-card class="p-5"><div class="text-slate-500">{{ $k }}</div><div class="text-2xl font-extrabold mt-2">{{ $v }}</div></x-card>@endforeach</div><div class="grid md:grid-cols-3 gap-5">@foreach(['Cash'=>'Rp 2.350.000','BCA'=>'Rp 12.750.000','Dana'=>'Rp 5.180.000','OVO'=>'Rp 4.300.000','Mandiri'=>'Rp 3.330.000'] as $k=>$v)<x-card class="p-6"><div class="font-bold text-lg">{{ $k }}</div><div class="text-2xl font-extrabold mt-3">{{ $v }}</div></x-card>@endforeach</div>
@endsection
