@extends('layouts.app')
@section('page_title', 'Dompet')
@section('page_subtitle', 'Kelola semua dompet dan rekening keluarga Anda')
@section('content')
    @php
        $formatCurrency = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
        $walletTypeLabels = [
            'cash' => 'Tunai',
            'bank' => 'Bank',
            'e-wallet' => 'E-Wallet',
        ];
        $walletPalette = [
            'cash' => ['tone' => 'cash', 'icon' => 'Rp'],
            'bank' => ['tone' => 'bank', 'icon' => 'BANK'],
            'e-wallet' => ['tone' => 'ewallet', 'icon' => 'EW'],
        ];
        $walletPayload = $wallets
            ->map(
                fn ($wallet) => [
                    'id' => $wallet->id,
                    'wallet_name' => $wallet->wallet_name,
                    'type' => $wallet->type,
                    'balance' => (float) $wallet->balance,
                    'account_number' => $wallet->account_number,
                    'update_url' => route('wallets.update', $wallet),
                    'destroy_url' => route('wallets.destroy', $wallet),
                ],
            )
            ->values();
    @endphp

    <div class="wallet-page"
        x-data="walletPage({
            storeUrl: @js(route('wallets.store')),
            wallets: @js($walletPayload),
        })">
        <section class="wallet-stat-grid">
            <x-card class="wallet-stat-card">
                <span class="wallet-stat-icon wallet-stat-total">
                    <img src="{{ asset('assets/svg/icon-wallet.svg') }}" alt="">
                </span>
                <div>
                    <span>Total Saldo</span>
                    <strong>{{ $formatCurrency($totalBalance) }}</strong>
                    <small class="text-emerald-600">Naik 8,5% dari bulan lalu</small>
                </div>
                <i>i</i>
            </x-card>

            <x-card class="wallet-stat-card">
                <span class="wallet-stat-icon wallet-stat-count">
                    <img src="{{ asset('assets/svg/icon-budget.svg') }}" alt="">
                </span>
                <div>
                    <span>Jumlah Dompet</span>
                    <strong>{{ $wallets->count() }}</strong>
                    <small>{{ $bankCount }} bank &bull; {{ $ewalletCount }} e-wallet</small>
                </div>
                <i>i</i>
            </x-card>

            <x-card class="wallet-stat-card">
                <span class="wallet-stat-icon wallet-stat-bank">
                    <img src="{{ asset('assets/svg/icon-shield.svg') }}" alt="">
                </span>
                <div>
                    <span>Saldo Bank</span>
                    <strong>{{ $formatCurrency($bankBalance) }}</strong>
                    <small>{{ $bankPercentage }}% dari total saldo</small>
                </div>
                <i>i</i>
            </x-card>

            <x-card class="wallet-stat-card">
                <span class="wallet-stat-icon wallet-stat-ewallet">
                    <img src="{{ asset('assets/svg/icon-wallet.svg') }}" alt="">
                </span>
                <div>
                    <span>Saldo E-Wallet</span>
                    <strong>{{ $formatCurrency($ewalletBalance) }}</strong>
                    <small>{{ $ewalletPercentage }}% dari total saldo</small>
                </div>
                <i>i</i>
            </x-card>
        </section>

        <x-card class="wallet-editor-card" x-show="showForm" x-cloak>
            <div class="wallet-card-header">
                <h2 x-text="mode === 'create' ? 'Tambah Dompet' : 'Edit Dompet'"></h2>
                <button type="button" class="wallet-close-button" @click="showForm = false">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 6l12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>
            <form method="POST" :action="formAction()" class="wallet-editor-form">
                @csrf
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="PATCH">
                </template>
                <label>
                    <span>Nama Dompet</span>
                    <input name="wallet_name" x-model="form.wallet_name" required>
                </label>
                <label>
                    <span>Tipe</span>
                    <select name="type" x-model="form.type" required>
                        <option value="cash">Cash</option>
                        <option value="bank">Bank</option>
                        <option value="e-wallet">E-Wallet</option>
                    </select>
                </label>
                <label>
                    <span>Saldo</span>
                    <input name="balance" type="number" min="0" step="1000" x-model="form.balance" required>
                </label>
                <label>
                    <span>Nomor Akun</span>
                    <input name="account_number" x-model="form.account_number">
                </label>
                <button type="submit" class="wallet-primary-button">
                    <span x-text="mode === 'create' ? 'Simpan Dompet' : 'Simpan Perubahan'"></span>
                </button>
            </form>
        </x-card>

        <section class="wallet-content-grid">
            <x-card class="wallet-chart-card">
                <div class="wallet-card-header">
                    <h2>Arus Saldo (Semua Dompet)</h2>
                    <span>{{ $period->translatedFormat('F Y') }}</span>
                </div>
                <div class="wallet-chart-legend">
                    <span><i class="bg-emerald-500"></i>Masuk</span>
                    <span><i class="bg-rose-500"></i>Keluar</span>
                </div>
                <div class="wallet-chart-wrap">
                    <canvas id="walletCashflowChart"></canvas>
                </div>
            </x-card>

            <x-card class="wallet-list-card">
                <div class="wallet-card-header">
                    <h2>Dompet Saya</h2>
                    <button type="button" @click="startCreate()">Kelola</button>
                </div>
                <div class="wallet-card-grid">
                    @forelse ($wallets as $wallet)
                        @php
                            $palette = $walletPalette[$wallet->type] ?? $walletPalette['cash'];
                            $account = $wallet->account_number ?: ($wallet->type === 'cash' ? 'Tunai' : 'Tanpa nomor akun');
                        @endphp
                        <article class="wallet-item-card">
                            <div class="wallet-item-head">
                                <span class="wallet-brand wallet-brand-{{ $palette['tone'] }}">
                                    {{ $wallet->type === 'bank' ? str($wallet->wallet_name)->substr(0, 3)->upper() : $palette['icon'] }}
                                </span>
                                <div>
                                    <strong>{{ $wallet->wallet_name }}</strong>
                                    <small>{{ $walletTypeLabels[$wallet->type] ?? ucfirst($wallet->type) }}</small>
                                </div>
                                <button type="button" @click="editWallet({{ $wallet->id }})">&rsaquo;</button>
                            </div>
                            <div class="wallet-item-balance">{{ $formatCurrency($wallet->balance) }}</div>
                            <div class="wallet-item-account">{{ $account }}</div>
                        </article>
                    @empty
                        <p class="wallet-empty">Belum ada dompet.</p>
                    @endforelse
                </div>
            </x-card>

            <div class="wallet-side-stack">
                <x-card class="wallet-quick-card">
                    <h2>Aksi Cepat</h2>
                    <button type="button" class="wallet-quick-action wallet-quick-primary" @click="startCreate()">
                        <span>+</span>
                        <strong>Tambah Dompet</strong>
                        <small>Tambah rekening bank atau e-wallet baru</small>
                    </button>
                    <button type="button" class="wallet-quick-action wallet-quick-blue">
                        <span>&harr;</span>
                        <strong>Transfer Antar Dompet</strong>
                        <small>Pindahkan saldo antar dompet Anda</small>
                    </button>
                    <button type="button" class="wallet-quick-action wallet-quick-purple">
                        <span>&#9776;</span>
                        <strong>Rekonsiliasi</strong>
                        <small>Cocokkan transaksi dengan mutasi akun</small>
                    </button>
                </x-card>

                <x-card class="wallet-tip-card">
                    <span>
                        <img src="{{ asset('assets/svg/icon-income.svg') }}" alt="">
                    </span>
                    <div>
                        <h2>Tips Keuangan</h2>
                        <p>Lakukan rekonsiliasi secara rutin agar catatan keuangan selalu akurat dan up to date.</p>
                    </div>
                </x-card>
            </div>
        </section>

        <x-card class="wallet-activity-card">
            <div class="wallet-card-header">
                <h2>Aktivitas Dompet Terbaru</h2>
                <a href="{{ route('reports.history') }}">Lihat semua</a>
            </div>
            <div class="wallet-table-wrap">
                <table class="wallet-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Dompet</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Saldo Setelahnya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentActivities as $activity)
                            <tr>
                                <td data-label="Tanggal">
                                    {{ $activity->transaction_date->translatedFormat('d M Y') }}
                                    <span>{{ $activity->created_at?->format('H:i') }}</span>
                                </td>
                                <td data-label="Dompet">
                                    <span class="wallet-mini-brand">{{ str($activity->wallet?->wallet_name ?? '-')->substr(0, 3)->upper() }}</span>
                                    {{ $activity->wallet?->wallet_name ?? '-' }}
                                </td>
                                <td data-label="Tipe">{{ $activity->type === 'income' ? 'Transfer Masuk' : 'Pengeluaran' }}</td>
                                <td data-label="Deskripsi">{{ $activity->title }}</td>
                                <td data-label="Masuk" class="wallet-money-in">
                                    {{ $activity->type === 'income' ? $formatCurrency($activity->amount) : '-' }}
                                </td>
                                <td data-label="Keluar" class="wallet-money-out">
                                    {{ $activity->type === 'expense' ? $formatCurrency($activity->amount) : '-' }}
                                </td>
                                <td data-label="Saldo Setelahnya">{{ $formatCurrency($activity->wallet?->balance ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="wallet-empty">Belum ada aktivitas dompet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <a href="{{ route('reports.history') }}" class="wallet-card-link">Lihat semua aktivitas &rarr;</a>
        </x-card>
    </div>
@endsection

@push('scripts')
    <script>
        window.walletPage = (config) => ({
            showForm: false,
            mode: 'create',
            storeUrl: config.storeUrl,
            wallets: config.wallets || [],
            form: {
                wallet_name: '',
                type: 'cash',
                balance: 0,
                account_number: '',
            },

            startCreate() {
                this.mode = 'create';
                this.showForm = true;
                this.form = {
                    wallet_name: '',
                    type: 'cash',
                    balance: 0,
                    account_number: '',
                };
            },

            editWallet(id) {
                const wallet = this.wallets.find((item) => item.id === id);

                if (!wallet) {
                    this.startCreate();
                    return;
                }

                this.mode = 'edit';
                this.showForm = true;
                this.form = {
                    id: wallet.id,
                    wallet_name: wallet.wallet_name,
                    type: wallet.type,
                    balance: wallet.balance,
                    account_number: wallet.account_number || '',
                    update_url: wallet.update_url,
                };
            },

            formAction() {
                return this.mode === 'edit' ? this.form.update_url : this.storeUrl;
            },
        });

        document.addEventListener('DOMContentLoaded', () => {
            const chartEl = document.getElementById('walletCashflowChart');

            if (!chartEl || !window.Chart) {
                return;
            }

            const cashflow = @js($cashflow);
            const month = @js($period->translatedFormat('M'));

            new window.Chart(chartEl, {
                type: 'line',
                data: {
                    labels: cashflow.labels.map((day) => `${day} ${month}`),
                    datasets: [
                        {
                            label: 'Masuk',
                            data: cashflow.income,
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.14)',
                            fill: true,
                            tension: 0.38,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            borderWidth: 3,
                        },
                        {
                            label: 'Keluar',
                            data: cashflow.expense,
                            borderColor: '#EF4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            fill: true,
                            tension: 0.38,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            borderWidth: 3,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${context.dataset.label}: Rp ${Number(context.raw).toLocaleString('id-ID')}`,
                            },
                        },
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { maxTicksLimit: 7 } },
                        y: {
                            grid: { color: '#E2E8F0', borderDash: [5, 5] },
                            ticks: {
                                callback: (value) => value >= 1000000 ? `${value / 1000000}jt` : value,
                            },
                        },
                    },
                },
            });
        });
    </script>
@endpush
