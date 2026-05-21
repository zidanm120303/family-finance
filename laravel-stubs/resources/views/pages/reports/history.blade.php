@extends('layouts.app')
@section('page_title','Laporan & Riwayat')
@section('page_subtitle','Pantau kinerja keuangan keluarga dan riwayat perubahan data')
@section('content')
<div class="grid xl:grid-cols-12 gap-6"><x-card class="p-6 xl:col-span-6"><h2 class="font-extrabold mb-4">Pemasukan vs Pengeluaran</h2><canvas id="reportChart" height="260"></canvas></x-card><x-card class="p-6 xl:col-span-6"><h2 class="font-extrabold mb-4">Riwayat Perubahan</h2><div class="space-y-3 text-sm">@foreach(['Create Gaji Bulanan','Update Tagihan Listrik','Delete Belanja Kopi','Create Pembayaran BPJS'] as $h)<div class="rounded-2xl border p-4">{{ $h }} — Budi Pratama</div>@endforeach</div></x-card></div><script type="module">import Chart from 'chart.js/auto';new Chart(document.getElementById('reportChart'),{type:'bar',data:{labels:['Jan','Feb','Mar','Apr','Mei'],datasets:[{label:'Pemasukan',data:[25,28,30,24,18]},{label:'Pengeluaran',data:[15,14,16,12,11]}]}});</script>
@endsection
