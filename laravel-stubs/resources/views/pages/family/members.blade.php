@extends('layouts.app')
@section('page_title','Anggota Keluarga')
@section('page_subtitle','Kelola anggota keluarga dan akses aplikasi FamFinance.')
@section('content')
<x-card class="p-6"><div class="flex justify-between mb-6"><h2 class="font-extrabold text-xl">Keluarga Pratama</h2><button class="rounded-2xl bg-emerald-600 text-white px-5 py-3 font-bold">+ Tambah Anggota</button></div><table class="w-full text-sm"><tbody>@foreach([['Budi Pratama','Kepala Keluarga','Aktif'],['Siti Pratiwi','Ibu','Aktif'],['Raka Pratama','Anak','Aktif'],['Ayu Pratama','Admin Keluarga','Nonaktif']] as $u)<tr class="border-t"><td class="p-4 font-bold">{{ $u[0] }}</td><td class="p-4">{{ strtolower(str_replace(' ','.',$u[0])) }}@email.com</td><td class="p-4">{{ $u[1] }}</td><td class="p-4">{{ $u[2] }}</td></tr>@endforeach</tbody></table></x-card>
@endsection
