@extends('layouts.app')
@section('page_title','Anggaran')
@section('page_subtitle','Kelola dan pantau anggaran keluarga Anda dengan mudah.')
@section('content')
<div class="grid md:grid-cols-4 gap-5 mb-6">@foreach(['Total Anggaran'=>'Rp 23.500.000','Terpakai'=>'Rp 15.420.000','Sisa Anggaran'=>'Rp 8.080.000','Kategori Melebihi Batas'=>'1'] as $k=>$v)<x-card class="p-5"><div class="text-slate-500">{{ $k }}</div><div class="text-2xl font-extrabold mt-2">{{ $v }}</div></x-card>@endforeach</div><x-card class="p-6"><h2 class="font-extrabold mb-4">Daftar Anggaran per Kategori</h2><div class="space-y-4">@foreach(['Listrik'=>75,'Internet'=>83,'BPJS'=>100,'Imunisasi'=>60,'Belanja Rumah Tangga'=>109,'Pendidikan'=>74] as $name=>$pct)<div><div class="flex justify-between font-semibold"><span>{{ $name }}</span><span>{{ $pct }}%</span></div><div class="h-2 rounded-full bg-slate-100 mt-2"><div class="h-2 rounded-full {{ $pct > 100 ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ min($pct,100) }}%"></div></div></div>@endforeach</div></x-card>
@endsection
