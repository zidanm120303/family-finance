<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - FamFinance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-[Inter] text-slate-950 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[minmax(0,1.09fr)_minmax(480px,.91fr)]">
        <section class="relative hidden overflow-hidden bg-gradient-to-br from-white via-emerald-50/30 to-cyan-50/45 px-8 py-8 lg:flex lg:flex-col xl:px-14 xl:py-10">
            <img src="{{ asset('assets/svg/logo-famfinance.svg') }}" class="h-14 w-fit" alt="FamFinance">

            <div class="mt-10 max-w-2xl">
                <h1 class="font-['Plus_Jakarta_Sans'] text-[38px] font-extrabold leading-[1.35] tracking-tight text-slate-950 xl:text-[42px]">
                    Kelola keuangan keluarga
                    <span class="block text-emerald-600">lebih mudah, bersama-sama.</span>
                </h1>
                <p class="mt-3 max-w-xl text-sm font-medium leading-6 text-slate-600 xl:text-base">
                    Catat pemasukan, atur pengeluaran, rencanakan anggaran, dan capai tujuan keuangan keluarga dengan transparan.
                </p>
            </div>

            <img src="{{ asset('assets/illustration/login-family-illustration.png') }}"
                class="mx-auto mt-3 h-[340px] w-full max-w-[670px] object-contain xl:h-[390px]" alt="Keluarga mengelola keuangan bersama">

            <div class="-mt-4 grid grid-cols-3 gap-3">
                @foreach([
                    ['Total Saldo', 'Rp 24.580.000', '↑ 8,5% dari bulan lalu', 'icon-wallet.svg', 'bg-emerald-50', 'text-emerald-600'],
                    ['Pengeluaran Bulan Ini', 'Rp 11.230.000', '↑ 5,6% dari bulan lalu', 'icon-expense.svg', 'bg-rose-50', 'text-rose-600'],
                    ['Anggaran Bulan Ini', 'Rp 7.520.000', '32% dari total anggaran', 'icon-budget.svg', 'bg-amber-50', 'text-amber-600'],
                ] as [$label, $value, $hint, $icon, $bgClass, $textClass])
                    <article class="rounded-2xl border border-slate-200/80 bg-white/95 p-3.5 shadow-[0_6px_20px_rgba(15,23,42,.06)]">
                        <div class="flex items-center gap-2.5">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl {{ $bgClass }}">
                                <img src="{{ asset('assets/svg/'.$icon) }}" class="h-5 w-5" alt="">
                            </span>
                            <div class="min-w-0">
                                <div class="truncate text-[10px] font-semibold text-slate-500">{{ $label }}</div>
                                <strong class="mt-1 block truncate text-sm font-extrabold">{{ $value }}</strong>
                            </div>
                        </div>
                        <p class="mt-2 pl-[46px] text-[9px] font-semibold {{ $textClass }}">{{ $hint }}</p>
                    </article>
                @endforeach
            </div>

            <div class="mt-4 grid grid-cols-3 gap-3 rounded-2xl border border-slate-200/80 bg-white/85 p-4">
                @foreach([
                    ['Aman', 'Data keluarga Anda terenkripsi dan terlindungi.', 'icon-shield.svg', 'bg-emerald-50'],
                    ['Mudah', 'Kelola keuangan keluarga kapan saja, di mana saja.', 'icon-lightning.svg', 'bg-blue-50'],
                    ['Transparan', 'Semua transaksi jelas dan bisa dilihat bersama.', 'icon-budget.svg', 'bg-violet-50'],
                ] as [$title, $text, $icon, $bgClass])
                    <div class="flex min-w-0 items-center gap-2.5">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full {{ $bgClass }}">
                            <img src="{{ asset('assets/svg/'.$icon) }}" class="h-5 w-5" alt="">
                        </span>
                        <div class="min-w-0">
                            <strong class="text-xs font-bold">{{ $title }}</strong>
                            <p class="mt-0.5 text-[9px] font-medium leading-4 text-slate-500">{{ $text }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-auto flex items-center justify-center gap-3 pt-5 text-[11px] font-medium text-slate-500">
                <img src="{{ asset('assets/svg/icon-shield.svg') }}" class="h-4 w-4" alt="">
                <span>Dipercaya keluarga Indonesia untuk mengatur keuangan lebih baik</span>
                <span class="font-bold text-amber-500">★★★★★</span>
                <span><strong class="text-slate-800">4.9/5</strong> dari 2.500+ keluarga</span>
            </div>
        </section>

        <section class="flex items-center justify-center bg-white p-4 sm:p-8">
            <form method="POST" action="{{ route('login.store') }}"
                class="w-full max-w-[560px] rounded-3xl border border-slate-200 bg-white px-6 py-8 shadow-[0_12px_45px_rgba(15,23,42,.08)] sm:px-10 sm:py-10 xl:px-14"
                x-data="{ showPassword: false }">
                @csrf
                <div class="text-center">
                    <img src="{{ asset('assets/svg/logo-famfinance.svg') }}" class="mx-auto h-14 w-fit" alt="FamFinance">
                    <h2 class="mt-7 font-['Plus_Jakarta_Sans'] text-3xl font-extrabold text-slate-950">Masuk ke Akun</h2>
                    <p class="mx-auto mt-2 max-w-sm text-sm font-medium leading-6 text-slate-500">
                        Selamat datang kembali! Kelola keuangan keluarga Anda dengan mudah dan aman.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-bold text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <label class="mt-7 block text-xs font-bold text-slate-700">
                    Email atau Username
                    <span class="mt-2 flex h-12 items-center gap-3 rounded-xl border border-slate-200 px-4 text-slate-500 transition focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-100">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M20 21a8 8 0 0 0-16 0M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <input name="login" value="{{ old('login') }}" class="min-w-0 flex-1 border-0 bg-transparent text-sm font-semibold text-slate-900 outline-none placeholder:font-medium placeholder:text-slate-400" placeholder="Masukkan email atau username Anda" autofocus required>
                    </span>
                </label>

                <label class="mt-5 block text-xs font-bold text-slate-700">
                    Password
                    <span class="mt-2 flex h-12 items-center gap-3 rounded-xl border border-slate-200 px-4 text-slate-500 transition focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-100">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 11V8a5 5 0 0 1 10 0v3M5 11h14v10H5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        </svg>
                        <input name="password" :type="showPassword ? 'text' : 'password'" class="min-w-0 flex-1 border-0 bg-transparent text-sm font-semibold text-slate-900 outline-none placeholder:font-medium placeholder:text-slate-400" placeholder="Masukkan password Anda" required>
                        <button type="button" @click="showPassword = !showPassword" class="grid h-8 w-8 place-items-center rounded-lg hover:bg-slate-50" aria-label="Tampilkan password">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Zm10 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="1.8"/>
                            </svg>
                        </button>
                    </span>
                </label>

                <div class="mt-4 flex items-center justify-between gap-3 text-xs">
                    <label class="inline-flex items-center gap-2 font-medium text-slate-500">
                        <input name="remember" value="1" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        Ingat saya
                    </label>
                    <span class="font-bold text-emerald-600">Lupa password?</span>
                </div>

                <button type="submit" class="mt-6 flex h-13 w-full items-center justify-center rounded-xl bg-emerald-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                    Masuk
                </button>

                <div class="my-5 flex items-center gap-4 text-[11px] font-medium text-slate-400">
                    <span class="h-px flex-1 bg-slate-200"></span><span>atau</span><span class="h-px flex-1 bg-slate-200"></span>
                </div>

                <button type="button" disabled title="Integrasi Google belum tersedia" class="flex h-12 w-full cursor-not-allowed items-center justify-center gap-3 rounded-xl border border-slate-200 text-sm font-bold text-slate-700 opacity-70">
                    <span class="text-lg font-extrabold text-blue-600">G</span>
                    Masuk dengan Google
                </button>

                <p class="mt-6 text-center text-sm font-medium text-slate-500">
                    Belum punya akun?
                    <a href="{{ route('register-family') }}" class="font-bold text-emerald-600">Daftar</a>
                </p>
            </form>
        </section>
    </main>
</body>
</html>
