<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'FamFinance') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-w-80 bg-slate-50 font-[Inter] text-slate-950 antialiased">
    <main class="min-h-screen">
        <section class="mx-auto grid min-h-screen w-full max-w-screen-2xl gap-8 px-4 py-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_minmax(24rem,0.8fr)] lg:items-center lg:px-8">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8 lg:p-10">
                <header class="flex flex-wrap items-center justify-between gap-4">
                    <a href="{{ url('/') }}" class="inline-flex items-center">
                        <img src="{{ asset('assets/svg/logo-famfinance.svg') }}" class="h-12 w-auto" alt="FamFinance">
                    </a>

                    @if (Route::has('login'))
                        <nav class="flex items-center gap-2">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-emerald-600 px-4 text-sm font-extrabold text-white hover:bg-emerald-700">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-700 hover:bg-slate-50">
                                    Masuk
                                </a>
                                @if (Route::has('register-family'))
                                    <a href="{{ route('register-family') }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-emerald-600 px-4 text-sm font-extrabold text-white hover:bg-emerald-700">
                                        Daftar
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </header>

                <div class="mt-12 grid gap-8 lg:grid-cols-[minmax(0,1fr)_18rem] lg:items-end">
                    <div>
                        <p class="inline-flex rounded-full bg-emerald-50 px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-emerald-700">
                            Family finance assistant
                        </p>
                        <h1 class="mt-5 max-w-3xl font-['Plus_Jakarta_Sans'] text-4xl font-extrabold leading-tight text-slate-950 sm:text-5xl">
                            Kelola keuangan keluarga lebih mudah, bersama-sama.
                        </h1>
                        <p class="mt-5 max-w-2xl text-base font-semibold leading-8 text-slate-600 sm:text-lg">
                            Catat pemasukan, pantau pengeluaran, atur anggaran, dan lihat aktivitas keluarga dalam satu ruang kerja yang rapi.
                        </p>

                        <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                            @guest
                                <a href="{{ Route::has('register-family') ? route('register-family') : route('login') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-emerald-600 px-5 text-sm font-extrabold text-white hover:bg-emerald-700">
                                    Mulai Sekarang
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-extrabold text-slate-700 hover:bg-slate-50">
                                    Masuk ke Akun
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-emerald-600 px-5 text-sm font-extrabold text-white hover:bg-emerald-700">
                                    Buka Dashboard
                                </a>
                            @endguest
                        </div>
                    </div>

                    <div class="grid gap-3">
                        <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-emerald-100">
                                    <img src="{{ asset('assets/svg/icon-wallet.svg') }}" class="h-7 w-7" alt="">
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-extrabold text-slate-500">Total Saldo</p>
                                    <strong class="mt-1 block text-lg font-extrabold text-slate-950">Rp 24.580.000</strong>
                                </div>
                            </div>
                        </article>
                        <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-rose-100">
                                    <img src="{{ asset('assets/svg/icon-expense.svg') }}" class="h-7 w-7" alt="">
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-extrabold text-slate-500">Pengeluaran</p>
                                    <strong class="mt-1 block text-lg font-extrabold text-slate-950">Rp 11.230.000</strong>
                                </div>
                            </div>
                        </article>
                        <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-amber-100">
                                    <img src="{{ asset('assets/svg/icon-budget.svg') }}" class="h-7 w-7" alt="">
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-extrabold text-slate-500">Anggaran</p>
                                    <strong class="mt-1 block text-lg font-extrabold text-slate-950">32% terpakai</strong>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>

            <aside class="grid gap-5">
                <div class="overflow-hidden rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                    <div class="rounded-3xl bg-emerald-50 p-6">
                        <img src="{{ asset('assets/illustration/login-family-illustration.png') }}" class="mx-auto h-80 w-full object-contain" alt="Ilustrasi keluarga mengelola keuangan">
                    </div>
                    <div class="mt-5 grid gap-3 sm:grid-cols-3">
                        <article class="rounded-2xl border border-slate-200 bg-white p-4">
                            <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-100">
                                <img src="{{ asset('assets/svg/icon-shield.svg') }}" class="h-5 w-5" alt="">
                            </span>
                            <strong class="mt-3 block text-sm font-extrabold text-slate-950">Aman</strong>
                            <p class="mt-1 text-xs font-semibold leading-5 text-slate-500">Data keluarga tetap terlindungi.</p>
                        </article>
                        <article class="rounded-2xl border border-slate-200 bg-white p-4">
                            <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-100">
                                <img src="{{ asset('assets/svg/icon-lightning.svg') }}" class="h-5 w-5" alt="">
                            </span>
                            <strong class="mt-3 block text-sm font-extrabold text-slate-950">Mudah</strong>
                            <p class="mt-1 text-xs font-semibold leading-5 text-slate-500">Transaksi tercatat cepat.</p>
                        </article>
                        <article class="rounded-2xl border border-slate-200 bg-white p-4">
                            <span class="grid h-10 w-10 place-items-center rounded-xl bg-violet-100">
                                <img src="{{ asset('assets/svg/icon-family.svg') }}" class="h-5 w-5" alt="">
                            </span>
                            <strong class="mt-3 block text-sm font-extrabold text-slate-950">Bersama</strong>
                            <p class="mt-1 text-xs font-semibold leading-5 text-slate-500">Semua anggota lebih transparan.</p>
                        </article>
                    </div>
                </div>

                <div class="flex flex-col gap-3 rounded-3xl border border-emerald-100 bg-emerald-50 p-5 text-sm font-bold text-slate-700 sm:flex-row sm:items-center sm:justify-between">
                    <p class="flex min-w-0 items-center gap-2">
                        <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-emerald-500"></span>
                        Dipercaya untuk kebiasaan finansial keluarga yang lebih sehat.
                    </p>
                    <span class="shrink-0 text-emerald-700">4.9/5 dari 2.500+ keluarga</span>
                </div>
            </aside>
        </section>
    </main>
</body>

</html>
