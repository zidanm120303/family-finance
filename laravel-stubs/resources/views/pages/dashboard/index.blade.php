@extends('layouts.app')
@section('title','Dashboard - FamFinance')
@section('page_title','Dashboard')
@section('page_subtitle','Selamat datang kembali, Budi Pratama 👋')
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
        @foreach([
            ['Total Saldo','Rp '.number_format($totalBalance ?? 24580000,0,',','.'),'icon-wallet.svg','text-emerald-600','↑ 8,5% dari bulan lalu'],
            ['Pemasukan Bulan Ini','Rp '.number_format($incomeMonth ?? 18750000,0,',','.'),'icon-income.svg','text-emerald-600','↑ 12,3% dari bulan lalu'],
            ['Pengeluaran Bulan Ini','Rp '.number_format($expenseMonth ?? 11230000,0,',','.'),'icon-expense.svg','text-rose-600','↑ 5,6% dari bulan lalu'],
            ['Sisa Anggaran','Rp 7.520.000','icon-budget.svg','text-amber-600','32% dari total anggaran'],
        ] as $card)
        <x-card class="p-5 flex items-center gap-4">
            <img src="{{ asset('assets/svg/'.$card[2]) }}" class="h-14 w-14" alt="">
            <div><div class="text-sm text-slate-500 font-semibold">{{ $card[0] }}</div><div class="text-2xl font-extrabold mt-1">{{ $card[1] }}</div><div class="text-xs mt-1 {{ $card[3] }}">{{ $card[4] }}</div></div>
        </x-card>
        @endforeach
    </div>
    <div class="flex flex-wrap justify-end gap-3">
        <a class="rounded-2xl bg-emerald-600 px-5 py-3 text-white font-bold" href="{{ route('transactions.create') }}">+ Tambah Transaksi</a>
        <a class="rounded-2xl bg-blue-600 px-5 py-3 text-white font-bold" href="#">+ Buat Anggaran</a>
        <a class="rounded-2xl bg-violet-600 px-5 py-3 text-white font-bold" href="#">+ Tambah Dompet</a>
    </div>
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <x-card class="p-6 xl:col-span-5"><div class="flex justify-between mb-4"><h2 class="font-extrabold text-lg">Arus Kas</h2><span class="text-sm text-slate-500">Bulan Ini</span></div><canvas id="cashflowChart" height="260"></canvas></x-card>
        <x-card class="p-6 xl:col-span-4"><div class="flex justify-between mb-4"><h2 class="font-extrabold text-lg">Anggaran per Kategori</h2><a class="text-emerald-600 text-sm font-bold">Lihat semua</a></div>@foreach(['Listrik'=>75,'Internet'=>83,'BPJS'=>100,'Imunisasi'=>50,'Belanja Rumah Tangga'=>60] as $name=>$pct)<div class="mb-4"><div class="flex justify-between text-sm font-semibold"><span>{{ $name }}</span><span>{{ $pct }}%</span></div><div class="h-2 bg-slate-100 rounded-full mt-2"><div class="h-2 rounded-full bg-emerald-500" style="width: {{ $pct }}%"></div></div></div>@endforeach</x-card>
        <x-card class="p-6 xl:col-span-3"><div class="flex justify-between mb-4"><h2 class="font-extrabold text-lg">Dompet</h2><a class="text-emerald-600 text-sm font-bold">Kelola</a></div>@foreach(($wallets ?? collect()) as $wallet)<div class="rounded-2xl border border-slate-200 p-4 mb-3"><div class="text-sm text-slate-500">{{ $wallet->wallet_name }}</div><div class="font-extrabold">Rp {{ number_format($wallet->balance,0,',','.') }}</div></div>@endforeach</x-card>
    </div>
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <x-card class="p-6 xl:col-span-6"><h2 class="font-extrabold text-lg mb-4">Transaksi Terbaru</h2><div class="overflow-x-auto"><table class="w-full text-sm"><tbody>@foreach(($transactions ?? collect()) as $trx)<tr class="border-t"><td class="py-3 font-semibold">{{ $trx->title }}</td><td>{{ $trx->category->category_name ?? '-' }}</td><td class="font-bold {{ $trx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">{{ $trx->type === 'income' ? '' : '-' }}Rp {{ number_format($trx->amount,0,',','.') }}</td><td>{{ $trx->transaction_date->format('d M Y') }}</td></tr>@endforeach</tbody></table></div></x-card>
        <x-card class="p-6 xl:col-span-3"><h2 class="font-extrabold text-lg mb-4">Pengeluaran per Kategori</h2><canvas id="donutChart" height="240"></canvas></x-card>
        <x-card class="p-6 xl:col-span-3"><h2 class="font-extrabold text-lg mb-4">Aktivitas Terbaru</h2><div class="space-y-4 text-sm text-slate-600"><p>✅ Anggaran Belanja Rumah Tangga diperbarui</p><p>👥 Siti menambahkan transaksi baru</p><p>🛡️ Pembayaran BPJS berhasil</p><p>📊 Laporan bulanan siap dilihat</p></div></x-card>
    </div>
</div>
<script type="module">
import Chart from 'chart.js/auto';
new Chart(document.getElementById('cashflowChart'), { type: 'line', data: { labels: ['1 Mei','5 Mei','10 Mei','15 Mei','20 Mei','25 Mei','31 Mei'], datasets: [{ label:'Pemasukan', data:[8,11,10,14,12,13,16] }, { label:'Pengeluaran', data:[3,5,4,8,6,6,9] }] } });
new Chart(document.getElementById('donutChart'), { type: 'doughnut', data: { labels: ['Belanja','Transportasi','Makanan','Tagihan','Kesehatan'], datasets: [{ data:[4250000,2350000,1980000,1450000,700000] }] } });
</script>
@endsection
