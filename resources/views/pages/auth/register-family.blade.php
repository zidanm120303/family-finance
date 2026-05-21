<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - FamFinance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="auth-body">
    <main class="auth-register-page">
        <header class="auth-register-topbar">
            <a href="{{ route('login') }}">
                <img src="{{ asset('assets/svg/logo-famfinance.svg') }}" alt="FamFinance">
            </a>
            <div>
                <span>Butuh bantuan?</span>
                <button type="button">Pusat Bantuan</button>
            </div>
        </header>

        <section class="auth-register-hero">
            <h1>Daftar & Buat Keluarga</h1>
            <p>Buat akun Anda dan siapkan keluarga untuk mulai mengelola keuangan bersama.</p>
        </section>

        <section class="auth-register-steps">
            <article class="is-active">
                <span>1</span>
                <strong>Akun</strong>
                <p>Buat akun Anda</p>
            </article>
            <article>
                <span>2</span>
                <strong>Data Keluarga</strong>
                <p>Lengkapi informasi keluarga</p>
            </article>
            <article>
                <span>3</span>
                <strong>Preferensi</strong>
                <p>Atur preferensi awal</p>
            </article>
        </section>

        <div class="auth-register-layout">
            <form method="POST" action="{{ route('register-family.store') }}" class="auth-register-card"
                x-data="{ showPassword: false, showConfirm: false }">
                @csrf
                <div class="auth-register-card-head">
                    <h2>Informasi Akun & Keluarga</h2>
                    <p><span></span> Data Anda aman dan hanya digunakan untuk keperluan akun.</p>
                </div>

                @if ($errors->any())
                    <div class="auth-error-box">{{ $errors->first() }}</div>
                @endif

                <section class="auth-form-section">
                    <h3>
                        <img src="{{ asset('assets/svg/icon-family.svg') }}" alt="">
                        Informasi Akun
                    </h3>
                    <div class="auth-register-grid">
                        <label class="auth-plain-field">
                            <span>Nama Lengkap</span>
                            <input name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Pratama" required>
                        </label>
                        <label class="auth-plain-field">
                            <span>Email</span>
                            <input name="email" type="email" value="{{ old('email') }}"
                                placeholder="Contoh: budi.pratama@email.com" required>
                        </label>
                        <label class="auth-plain-field">
                            <span>Username</span>
                            <input name="username" value="{{ old('username') }}" placeholder="Contoh: budipratama">
                        </label>
                        <label class="auth-plain-field auth-phone-field">
                            <span>Nomor HP</span>
                            <i>+62</i>
                            <input name="phone" value="{{ old('phone') }}" placeholder="812-3456-7890">
                        </label>
                        <label class="auth-plain-field">
                            <span>Password</span>
                            <button type="button" @click="showPassword = !showPassword" title="Tampilkan password">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z" />
                                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                </svg>
                            </button>
                            <input name="password" :type="showPassword ? 'text' : 'password'"
                                placeholder="Buat password minimal 8 karakter" required>
                        </label>
                        <label class="auth-plain-field">
                            <span>Konfirmasi Password</span>
                            <button type="button" @click="showConfirm = !showConfirm" title="Tampilkan konfirmasi">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z" />
                                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                </svg>
                            </button>
                            <input name="password_confirmation" :type="showConfirm ? 'text' : 'password'"
                                placeholder="Ulangi password Anda" required>
                        </label>
                    </div>
                </section>

                <section class="auth-form-section">
                    <h3>
                        <img src="{{ asset('assets/svg/icon-family.svg') }}" alt="">
                        Informasi Keluarga
                    </h3>
                    <div class="auth-family-grid">
                        <div class="auth-register-grid auth-family-fields">
                            <div class="auth-family-top-row">
                                <label class="auth-plain-field">
                                    <span>Nama Keluarga</span>
                                    <input name="family_name" value="{{ old('family_name') }}"
                                        placeholder="Contoh: Keluarga Pratama" required>
                                </label>
                                <label class="auth-plain-field auth-family-code-field">
                                    <span>Kode Keluarga</span>
                                    <input name="family_code" value="{{ old('family_code') }}"
                                        placeholder="Contoh: PRATAMA2024">
                                    <small>Digunakan untuk mengundang anggota keluarga.</small>
                                </label>
                            </div>
                            <label class="auth-plain-field auth-wide-field">
                                <span>Alamat</span>
                                <textarea name="address" rows="3" placeholder="Contoh: Jl. Melati No. 10, RT 02/RW 05"
                                    required>{{ old('address') }}</textarea>
                            </label>
                            <label class="auth-plain-field">
                                <span>Kota</span>
                                <input name="city" value="{{ old('city') }}" placeholder="Contoh: Jakarta Selatan"
                                    required>
                            </label>
                            <label class="auth-plain-field">
                                <span>Provinsi</span>
                                <input name="province" value="{{ old('province') }}" placeholder="Contoh: DKI Jakarta"
                                    required>
                            </label>
                            <label class="auth-plain-field">
                                <span>Kode Pos</span>
                                <input name="postal_code" value="{{ old('postal_code') }}" placeholder="Contoh: 12450"
                                    required>
                            </label>
                            <label class="auth-plain-field">
                                <span>Telepon Keluarga (Opsional)</span>
                                <input name="family_phone" value="{{ old('family_phone') }}" placeholder="Contoh: 021-1234567">
                            </label>
                        </div>

                        <aside class="auth-role-card">
                            <h3>Peran Anda dalam Keluarga</h3>
                            <p>Pilih peran Anda untuk menentukan hak akses di dalam keluarga.</p>
                            <label class="is-selected">
                                <input type="radio" checked disabled>
                                <span>
                                    <strong>Kepala Keluarga</strong>
                                    <small>Memiliki akses penuh untuk mengelola keuangan keluarga dan mengundang anggota
                                        lainnya.</small>
                                </span>
                            </label>
                            <label>
                                <input type="radio" disabled>
                                <span>
                                    <strong>Anggota Keluarga</strong>
                                    <small>Dapat melihat dan mencatat transaksi sesuai izin yang diberikan.</small>
                                </span>
                            </label>
                        </aside>
                    </div>
                </section>

                <section class="auth-preference-card">
                    <h3>Pengaturan Awal</h3>
                    <label>
                        <span class="auth-pref-icon">
                            <img src="{{ asset('assets/svg/icon-category-health.svg') }}" alt="">
                        </span>
                        <span>
                            <strong>Buat kategori default</strong>
                            <small>Gunakan kategori pemasukan dan pengeluaran standar untuk memudahkan pencatatan.</small>
                        </span>
                        <input name="create_defaults" value="1" type="checkbox" checked>
                    </label>
                    <label>
                        <span class="auth-pref-icon">
                            <img src="{{ asset('assets/svg/icon-family.svg') }}" alt="">
                        </span>
                        <span>
                            <strong>Undang anggota keluarga nanti</strong>
                            <small>Anda dapat mengundang anggota keluarga kapan saja setelah pendaftaran selesai.</small>
                        </span>
                        <input type="checkbox" checked>
                    </label>
                </section>

                <div class="auth-register-actions">
                    <a href="{{ route('login') }}">
                        <span>&larr;</span>
                        Kembali ke Login
                    </a>
                    <button type="submit">
                        Buat Akun & Keluarga
                        <span>&rarr;</span>
                    </button>
                </div>

                <p class="auth-register-terms">
                    Dengan membuat akun, Anda menyetujui
                    <a href="#">Syarat & Ketentuan</a>
                    dan
                    <a href="#">Kebijakan Privasi</a>
                    kami.
                </p>
            </form>

            <aside class="auth-register-side">
                <div class="auth-register-illustration">
                    <img src="{{ asset('assets/illustration/family-finance-security-illustration.png') }}"
                        alt="Ilustrasi keluarga">
                </div>
                <h2>Kelola keuangan keluarga <span>lebih mudah bersama FamFinance</span></h2>
                <div class="auth-benefit-list">
                    <article>
                        <span class="auth-benefit-green">
                            <img src="{{ asset('assets/svg/icon-wallet.svg') }}" alt="">
                        </span>
                        <div>
                            <strong>Transparansi Keuangan</strong>
                            <p>Semua pemasukan dan pengeluaran tercatat jelas dan dapat dilihat bersama.</p>
                        </div>
                    </article>
                    <article>
                        <span class="auth-benefit-red">
                            <img src="{{ asset('assets/svg/icon-expense.svg') }}" alt="">
                        </span>
                        <div>
                            <strong>Perencanaan Lebih Baik</strong>
                            <p>Susun anggaran, pantau pengeluaran, dan capai tujuan keuangan keluarga.</p>
                        </div>
                    </article>
                    <article>
                        <span class="auth-benefit-blue">
                            <img src="{{ asset('assets/svg/icon-shield.svg') }}" alt="">
                        </span>
                        <div>
                            <strong>Aman & Terlindungi</strong>
                            <p>Data Anda dienkripsi dan tidak dibagikan ke pihak ketiga.</p>
                        </div>
                    </article>
                    <article>
                        <span class="auth-benefit-amber">
                            <img src="{{ asset('assets/svg/icon-budget.svg') }}" alt="">
                        </span>
                        <div>
                            <strong>Akses Fleksibel</strong>
                            <p>Kelola keuangan di mana saja, kapan saja melalui perangkat favorit Anda.</p>
                        </div>
                    </article>
                </div>

                <div class="auth-start-tip">
                    <h3>Tips Memulai</h3>
                    <p>Isi data dengan benar untuk pengalaman terbaik</p>
                    <p>Gunakan kode keluarga untuk mengundang anggota lainnya</p>
                    <p>Anda bisa mengubah pengaturan kapan saja di menu Pengaturan</p>
                </div>
            </aside>
        </div>
    </main>
</body>

</html>
