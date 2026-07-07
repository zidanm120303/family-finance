<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Keluarga - FamFinance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-[Inter] text-slate-950 antialiased">
    @php
        $fieldClass = 'mt-1.5 h-10 w-full min-w-0 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-800 outline-none transition placeholder:font-medium placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100';
        $labelClass = 'block min-w-0 text-[11px] font-bold text-slate-700';
    @endphp

    <header class="flex h-[70px] items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-7">
        <a href="{{ route('login') }}"><img src="{{ asset('assets/svg/logo-famfinance.svg') }}" class="h-11 w-auto" alt="FamFinance"></a>
        <div class="flex items-center gap-3 text-[11px] font-medium text-slate-500">
            <span class="hidden sm:inline">Butuh bantuan?</span>
            <span class="rounded-lg border border-slate-200 px-3 py-2 font-bold text-slate-700">Pusat Bantuan</span>
        </div>
    </header>

    <main class="mx-auto w-full max-w-[1280px] px-4 py-7 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="font-['Plus_Jakarta_Sans'] text-2xl font-extrabold">Daftar / Gabung Keluarga</h1>
            <p class="mt-2 text-xs font-medium text-slate-500">Buat keluarga baru sebagai Kepala Keluarga atau bergabung sebagai Ibu Rumah Tangga.</p>
        </div>

        <div class="mx-auto mt-6 grid max-w-4xl grid-cols-3 gap-2 sm:gap-4">
            @foreach([
                ['1', 'Akun', 'Buat akun Anda', true],
                ['2', 'Data Keluarga', 'Buat atau gabung keluarga', false],
                ['3', 'Preferensi', 'Atur preferensi awal', false],
            ] as [$number, $title, $text, $active])
                <div class="relative min-w-0">
                    @if(!$loop->last)
                        <span class="absolute left-7 right-[-1rem] top-3 h-px {{ $active ? 'bg-emerald-500' : 'bg-slate-200' }}"></span>
                    @endif
                    <div class="relative">
                        <span class="grid h-6 w-6 place-items-center rounded-full border text-[10px] font-extrabold {{ $active ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white text-slate-600' }}">{{ $number }}</span>
                        <strong class="mt-2 block truncate text-[11px] {{ $active ? 'text-emerald-700' : 'text-slate-700' }}">{{ $title }}</strong>
                        <span class="mt-0.5 hidden text-[9px] font-medium text-slate-500 sm:block">{{ $text }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('register-family.store') }}" class="mt-5 grid items-start gap-5 lg:grid-cols-[minmax(0,1fr)_350px]" x-data="{ showPassword: false, showConfirm: false, accountRole: @js(old('account_role', 'kepala_keluarga')) }">
            @csrf
            <section class="ff-card p-4 sm:p-5">
                <div>
                    <h2 class="text-sm font-extrabold">Informasi Akun &amp; Keluarga</h2>
                    <p class="mt-1 flex items-center gap-2 text-[10px] font-medium text-slate-500">
                        <img src="{{ asset('assets/svg/icon-shield.svg') }}" class="h-3.5 w-3.5" alt="">
                        Data Anda aman dan hanya digunakan untuk keperluan akun.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700">{{ $errors->first() }}</div>
                @endif

                <div class="mt-5 flex items-center gap-2 text-xs font-extrabold">
                    <img src="{{ asset('assets/svg/icon-family.svg') }}" class="h-4 w-4" alt=""> Informasi Akun
                </div>
                <div class="mt-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <label class="{{ $labelClass }}">Nama Lengkap
                        <input name="name" value="{{ old('name') }}" class="{{ $fieldClass }}" placeholder="Contoh: Budi Pratama" required>
                    </label>
                    <label class="{{ $labelClass }}">Email
                        <input name="email" type="email" value="{{ old('email') }}" class="{{ $fieldClass }}" placeholder="budi.pratama@email.com" required>
                    </label>
                    <label class="{{ $labelClass }}">Username
                        <input name="username" value="{{ old('username') }}" class="{{ $fieldClass }}" placeholder="budipratama">
                    </label>
                    <label class="{{ $labelClass }}">Nomor HP
                        <input name="phone" value="{{ old('phone') }}" class="{{ $fieldClass }}" placeholder="0812-3456-7890">
                    </label>
                    <label class="{{ $labelClass }}">Password
                        <span class="mt-1.5 flex h-10 items-center rounded-lg border border-slate-200 px-3 focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-100">
                            <input name="password" :type="showPassword ? 'text' : 'password'" class="min-w-0 flex-1 border-0 bg-transparent text-xs font-semibold outline-none placeholder:font-medium placeholder:text-slate-400" placeholder="Minimal 8 karakter" required>
                            <button type="button" @click="showPassword = !showPassword" class="text-[10px] font-bold text-emerald-700">Lihat</button>
                        </span>
                    </label>
                    <label class="{{ $labelClass }}">Konfirmasi Password
                        <span class="mt-1.5 flex h-10 items-center rounded-lg border border-slate-200 px-3 focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-100">
                            <input name="password_confirmation" :type="showConfirm ? 'text' : 'password'" class="min-w-0 flex-1 border-0 bg-transparent text-xs font-semibold outline-none placeholder:font-medium placeholder:text-slate-400" placeholder="Ulangi password Anda" required>
                            <button type="button" @click="showConfirm = !showConfirm" class="text-[10px] font-bold text-emerald-700">Lihat</button>
                        </span>
                    </label>
                </div>

                <div class="mt-5 flex items-center gap-2 text-xs font-extrabold">
                    <img src="{{ asset('assets/svg/icon-family.svg') }}" class="h-4 w-4" alt="">
                    <span x-text="accountRole === 'kepala_keluarga' ? 'Informasi Keluarga Baru' : 'Gabung Keluarga'"></span>
                </div>
                <div class="mt-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-12">
                    <label x-show="accountRole === 'kepala_keluarga'" x-cloak class="{{ $labelClass }} xl:col-span-5">Nama Keluarga
                        <input name="family_name" value="{{ old('family_name') }}" class="{{ $fieldClass }}" placeholder="Contoh: Keluarga Pratama" :required="accountRole === 'kepala_keluarga'">
                    </label>
                    <label class="{{ $labelClass }}" :class="accountRole === 'kepala_keluarga' ? 'xl:col-span-4' : 'sm:col-span-2 xl:col-span-9'">Kode Keluarga
                        <input name="family_code" value="{{ old('family_code') }}" class="{{ $fieldClass }}" placeholder="FF12345" :required="accountRole === 'ibu_rumah_tangga'">
                        <span x-show="accountRole === 'kepala_keluarga'" class="mt-1 block text-[9px] font-medium text-slate-500">Opsional. Jika kosong, sistem membuat kode pendek otomatis.</span>
                        <span x-show="accountRole === 'ibu_rumah_tangga'" x-cloak class="mt-1 block text-[9px] font-medium text-slate-500">Minta kode ini dari Kepala Keluarga, contoh: FF12345.</span>
                    </label>
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 p-3 sm:row-span-3 xl:col-span-3 xl:row-span-3">
                        <strong class="text-[11px] text-emerald-800">Peran Anda dalam Keluarga</strong>
                        <p class="mt-2 text-[9px] font-medium leading-4 text-slate-500">Pilih peran Anda untuk menentukan cara akun terhubung ke keluarga.</p>
                        <div class="mt-4 grid gap-4">
                            <label class="flex cursor-pointer items-start gap-3">
                                <input type="radio" name="account_role" value="kepala_keluarga" x-model="accountRole" class="peer sr-only" @checked(old('account_role', 'kepala_keluarga') === 'kepala_keluarga')>
                                <span class="mt-0.5 h-4 w-4 shrink-0 rounded-full border border-slate-300 bg-white peer-checked:border-[5px] peer-checked:border-emerald-600"></span>
                                <span>
                                    <b class="block text-[11px]">Kepala Keluarga</b>
                                    <span class="mt-1 block text-[9px] font-medium leading-4 text-slate-500">Membuat keluarga baru, mengatur anggota, dan menjadi pemilik kode keluarga.</span>
                                </span>
                            </label>
                            <label class="flex cursor-pointer items-start gap-3">
                                <input type="radio" name="account_role" value="ibu_rumah_tangga" x-model="accountRole" class="peer sr-only" @checked(old('account_role') === 'ibu_rumah_tangga')>
                                <span class="mt-0.5 h-4 w-4 shrink-0 rounded-full border border-slate-300 bg-white peer-checked:border-[5px] peer-checked:border-emerald-600"></span>
                                <span>
                                    <b class="block text-[11px]">Ibu Rumah Tangga</b>
                                    <span class="mt-1 block text-[9px] font-medium leading-4 text-slate-500">Bergabung ke keluarga yang sudah dibuat memakai kode keluarga.</span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <label x-show="accountRole === 'kepala_keluarga'" x-cloak class="{{ $labelClass }} sm:col-span-2 xl:col-span-9">Alamat
                        <textarea name="address" rows="2" class="{{ $fieldClass }} h-16 py-2.5" placeholder="Contoh: Jl. Melati No. 10" :required="accountRole === 'kepala_keluarga'">{{ old('address') }}</textarea>
                    </label>
                    <label x-show="accountRole === 'kepala_keluarga'" x-cloak class="{{ $labelClass }} xl:col-span-3">Kota
                        <input name="city" value="{{ old('city') }}" class="{{ $fieldClass }}" placeholder="Jakarta Selatan" :required="accountRole === 'kepala_keluarga'">
                    </label>
                    <label x-show="accountRole === 'kepala_keluarga'" x-cloak class="{{ $labelClass }} xl:col-span-3">Provinsi
                        <input name="province" value="{{ old('province') }}" class="{{ $fieldClass }}" placeholder="DKI Jakarta" :required="accountRole === 'kepala_keluarga'">
                    </label>
                    <label x-show="accountRole === 'kepala_keluarga'" x-cloak class="{{ $labelClass }} xl:col-span-3">Kode Pos
                        <input name="postal_code" value="{{ old('postal_code') }}" class="{{ $fieldClass }}" placeholder="12450" :required="accountRole === 'kepala_keluarga'">
                    </label>
                    <label x-show="accountRole === 'kepala_keluarga'" x-cloak class="{{ $labelClass }} xl:col-span-4">Telepon Keluarga (Opsional)
                        <input name="family_phone" value="{{ old('family_phone') }}" class="{{ $fieldClass }}" placeholder="021-1234567">
                    </label>
                </div>

                <div x-show="accountRole === 'kepala_keluarga'" x-cloak class="mt-5 rounded-xl border border-slate-200 p-3">
                    <strong class="text-[11px]">Pengaturan Awal</strong>
                    <label class="mt-2 flex items-center justify-between gap-4 rounded-lg bg-slate-50 p-3">
                        <span class="flex min-w-0 items-start gap-3">
                            <img src="{{ asset('assets/svg/icon-category-health.svg') }}" class="h-7 w-7 shrink-0" alt="">
                            <span>
                                <b class="block text-[10px]">Buat kategori default</b>
                                <span class="text-[9px] font-medium text-slate-500">Gunakan kategori pemasukan dan pengeluaran standar.</span>
                            </span>
                        </span>
                        <input name="create_defaults" value="1" type="checkbox" class="h-5 w-5 shrink-0 rounded-full border-slate-300 text-emerald-600 focus:ring-emerald-500" :disabled="accountRole !== 'kepala_keluarga'" @checked(old('create_defaults', true))>
                    </label>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-[.75fr_1.25fr]">
                    <a href="{{ route('login') }}" class="secondary-action">&larr; Kembali ke Login</a>
                    <button class="primary-action"><span x-text="accountRole === 'kepala_keluarga' ? 'Buat Akun & Keluarga' : 'Gabung ke Keluarga'"></span> <span aria-hidden="true">-&gt;</span></button>
                </div>
                <p class="mt-3 text-center text-[9px] font-medium text-slate-500">Dengan membuat akun, Anda menyetujui <span class="text-emerald-600">Syarat &amp; Ketentuan</span> dan <span class="text-emerald-600">Kebijakan Privasi</span>.</p>
            </section>

            <aside class="ff-card p-5 lg:sticky lg:top-5">
                <img src="{{ asset('assets/illustration/family-finance-security-illustration.png') }}" class="mx-auto h-40 w-full object-contain" alt="Keluarga FamFinance">
                <h2 class="mt-1 text-center font-['Plus_Jakarta_Sans'] text-lg font-extrabold leading-7">Kelola keuangan keluarga<br>lebih mudah bersama <span class="text-emerald-600">FamFinance</span></h2>
                <div class="mt-5 grid gap-4">
                    @foreach([
                        ['Transparansi Keuangan', 'Semua pemasukan dan pengeluaran tercatat jelas dan dapat dilihat bersama.', 'icon-wallet.svg', 'bg-emerald-50'],
                        ['Perencanaan Lebih Baik', 'Susun anggaran, pantau pengeluaran, dan capai tujuan keuangan keluarga.', 'icon-expense.svg', 'bg-rose-50'],
                        ['Aman & Terlindungi', 'Data keluarga dilindungi dan akses dikelola berdasarkan peran.', 'icon-shield.svg', 'bg-blue-50'],
                        ['Akses Fleksibel', 'Kelola keuangan kapan saja melalui perangkat favorit Anda.', 'icon-budget.svg', 'bg-amber-50'],
                    ] as [$title, $text, $icon, $tone])
                        <div class="flex gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl {{ $tone }}"><img src="{{ asset('assets/svg/'.$icon) }}" class="h-5 w-5" alt=""></span>
                            <div>
                                <strong class="text-[11px]">{{ $title }}</strong>
                                <p class="mt-1 text-[9px] font-medium leading-4 text-slate-500">{{ $text }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 rounded-xl border border-emerald-100 bg-emerald-50/60 p-4">
                    <strong class="text-[11px] text-emerald-800">Tips Memulai</strong>
                    <ul class="mt-2 grid gap-2 text-[9px] font-medium leading-4 text-slate-500">
                        <li>✓ Isi data dengan benar untuk pengalaman terbaik</li>
                        <li>✓ Gunakan kode keluarga untuk identitas keluarga</li>
                        <li>✓ Pengaturan dapat diubah kapan saja</li>
                    </ul>
                </div>
            </aside>
        </form>
    </main>
</body>
</html>
