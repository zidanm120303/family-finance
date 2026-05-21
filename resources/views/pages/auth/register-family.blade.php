<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - FamFinance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-50 font-[Inter] text-slate-950">
<main class="p-4 sm:p-6 lg:p-10">
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <img src="{{ asset('assets/svg/logo-famfinance.svg') }}" class="h-14" alt="FamFinance">
            <a href="{{ route('login') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-extrabold text-slate-700">Masuk</a>
        </div>

        <div class="mt-10 grid gap-8 xl:grid-cols-12">
            <form method="POST" action="{{ route('register-family.store') }}" class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm sm:p-8 xl:col-span-8">
                @csrf
                <div class="flex flex-wrap gap-3">
                    <x-badge tone="success">Akun</x-badge>
                    <x-badge tone="blue">Data Keluarga</x-badge>
                    <x-badge tone="purple">Preferensi</x-badge>
                </div>
                <h1 class="mt-6 font-display text-2xl font-extrabold sm:text-3xl">Daftar & Buat Keluarga</h1>
                <p class="mt-2 text-slate-500">Role pertama otomatis menjadi Kepala Keluarga.</p>

                @if($errors->any())
                    <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="mt-8 grid gap-5 md:grid-cols-2">
                    <label class="text-sm font-extrabold text-slate-700">Nama Lengkap<input name="name" value="{{ old('name') }}" class="form-field mt-2"></label>
                    <label class="text-sm font-extrabold text-slate-700">Email<input name="email" value="{{ old('email') }}" class="form-field mt-2"></label>
                    <label class="text-sm font-extrabold text-slate-700">Username<input name="username" value="{{ old('username') }}" class="form-field mt-2"></label>
                    <label class="text-sm font-extrabold text-slate-700">Nomor HP<input name="phone" value="{{ old('phone') }}" class="form-field mt-2"></label>
                    <label class="text-sm font-extrabold text-slate-700">Password<input name="password" type="password" class="form-field mt-2"></label>
                    <label class="text-sm font-extrabold text-slate-700">Konfirmasi Password<input name="password_confirmation" type="password" class="form-field mt-2"></label>
                    <label class="text-sm font-extrabold text-slate-700">Nama Keluarga<input name="family_name" value="{{ old('family_name') }}" class="form-field mt-2"></label>
                    <label class="text-sm font-extrabold text-slate-700">Kode Keluarga<input name="family_code" value="{{ old('family_code') }}" class="form-field mt-2" placeholder="Opsional"></label>
                    <label class="text-sm font-extrabold text-slate-700">Kota<input name="city" value="{{ old('city') }}" class="form-field mt-2"></label>
                    <label class="text-sm font-extrabold text-slate-700">Provinsi<input name="province" value="{{ old('province') }}" class="form-field mt-2"></label>
                    <label class="text-sm font-extrabold text-slate-700">Kode Pos<input name="postal_code" value="{{ old('postal_code') }}" class="form-field mt-2"></label>
                    <label class="flex items-center gap-3 pt-8 text-sm font-extrabold text-slate-700">
                        <input name="create_defaults" value="1" type="checkbox" checked class="rounded border-slate-300 text-emerald-600">
                        Buat kategori dan dompet default
                    </label>
                    <label class="md:col-span-2 text-sm font-extrabold text-slate-700">Alamat<textarea name="address" class="form-field mt-2" rows="4">{{ old('address') }}</textarea></label>
                </div>
                <x-button type="submit" class="mt-8 w-full py-4">Buat Akun & Keluarga</x-button>
            </form>

            <aside class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm sm:p-8 xl:col-span-4">
                <img src="{{ asset('assets/illustration/family-finance-security-illustration.png') }}" class="mx-auto h-52 object-contain sm:h-64" alt="Ilustrasi keamanan keluarga">
                <h2 class="mt-6 text-center font-display text-2xl font-extrabold">Transparansi keuangan keluarga dari hari pertama.</h2>
                <div class="mt-8 space-y-4">
                    <div class="rounded-2xl bg-emerald-50 p-4 font-bold text-emerald-800">Kategori default langsung tersedia.</div>
                    <div class="rounded-2xl bg-blue-50 p-4 font-bold text-blue-800">Kode keluarga siap dibagikan.</div>
                    <div class="rounded-2xl bg-violet-50 p-4 font-bold text-violet-800">Role anggota bisa dikelola.</div>
                </div>
            </aside>
        </div>
    </div>
</main>
</body>
</html>
