<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - FamFinance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-[Inter] text-slate-950">
    <main class="min-h-screen grid lg:grid-cols-2">
        <section class="relative overflow-hidden bg-emerald-50 px-5 py-8 sm:px-8 sm:py-10 lg:px-16 lg:py-14">
            <img src="{{ asset('assets/svg/logo-famfinance.svg') }}" class="h-14 w-fit" alt="FamFinance">
            <div class="mt-8 max-w-2xl sm:mt-12">
                <h1 class="font-display text-3xl font-extrabold leading-tight sm:text-4xl lg:text-6xl">Keuangan keluarga
                    rapi dalam satu ruang bersama.</h1>
                <p class="mt-4 text-base leading-7 text-slate-600 sm:mt-5 sm:text-lg sm:leading-8">Pantau saldo,
                    anggaran, riwayat transaksi, dan dompet keluarga dengan data yang transparan.</p>
            </div>
            <img src="{{ asset('assets/illustration/login-family-illustration.png') }}"
                class="mt-8 h-56 w-full max-w-2xl object-contain object-left sm:mt-10 sm:h-[300px] lg:h-[340px]"
                alt="Ilustrasi keluarga">
            <div class="grid max-w-3xl gap-4 sm:grid-cols-3">
                <div class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm">
                    <div class="text-2xl font-extrabold">8+</div>
                    <p class="mt-1 text-sm font-semibold text-slate-500">Kategori siap pakai</p>
                </div>
                <div class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm">
                    <div class="text-2xl font-extrabold">4</div>
                    <p class="mt-1 text-sm font-semibold text-slate-500">Role keluarga</p>
                </div>
                <div class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm">
                    <div class="text-2xl font-extrabold">100%</div>
                    <p class="mt-1 text-sm font-semibold text-slate-500">Blade Laravel</p>
                </div>
            </div>
        </section>

        <section class="flex items-center justify-center p-4 sm:p-6 lg:p-10">
            <form method="POST" action="{{ route('login.store') }}"
                class="w-full max-w-xl rounded-[24px] border border-slate-200 bg-white p-5 shadow-[0_24px_60px_rgba(15,23,42,0.10)] sm:p-8 lg:p-10">
                @csrf
                <div class="text-center">
                    <img src="{{ asset('assets/svg/logo-famfinance.svg') }}"
                        class="w-24 md:w-64 h-auto object-cover mx-auto" alt="FamFinance">
                    <h2 class="mt-8 font-display text-3xl font-extrabold sm:text-4xl">Masuk ke Akun</h2>
                    <p class="mt-3 text-slate-500">Gunakan akun dummy: budi.pratama@email.com / password</p>
                </div>

                @if ($errors->any())
                    <div
                        class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <label class="mt-8 block text-sm font-extrabold text-slate-700">
                    Email atau Username
                    <input name="login" value="{{ old('login') }}" class="form-field mt-2"
                        placeholder="budi.pratama@email.com" autofocus>
                </label>
                <label class="mt-5 block text-sm font-extrabold text-slate-700">
                    Password
                    <input name="password" type="password" class="form-field mt-2" placeholder="Masukkan password">
                </label>
                <div class="mt-5 flex flex-wrap items-center justify-between gap-3 text-sm">
                    <label class="flex items-center gap-2 font-semibold text-slate-500">
                        <input name="remember" value="1" type="checkbox"
                            class="rounded border-slate-300 text-emerald-600">
                        Ingat saya
                    </label>
                    <span class="font-extrabold text-emerald-600">Lupa password?</span>
                </div>
                <x-button type="submit" class="mt-8 w-full py-4">Masuk</x-button>
                <button type="button"
                    class="mt-4 w-full rounded-2xl border border-slate-200 py-4 font-extrabold text-slate-700">Masuk
                    dengan Google</button>
                <p class="mt-6 text-center text-slate-500">Belum punya akun? <a href="{{ route('register-family') }}"
                        class="font-extrabold text-emerald-600">Daftar keluarga</a></p>
            </form>
        </section>
    </main>
</body>

</html>
