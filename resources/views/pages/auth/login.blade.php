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

<body class="auth-body">
    <main class="auth-login-page">
        <section class="auth-login-showcase">
            <img src="{{ asset('assets/svg/logo-famfinance.svg') }}" class="auth-login-logo" alt="FamFinance">

            <div class="auth-login-copy">
                <h1>Kelola keuangan keluarga <span>lebih mudah, bersama-sama.</span></h1>
                <p>Catat pemasukan, atur pengeluaran, rencanakan anggaran, dan capai tujuan keuangan keluarga dengan
                    transparan.</p>
            </div>

            <div class="auth-family-visual">
                <img src="{{ asset('assets/illustration/login-family-illustration.png') }}" alt="Ilustrasi keluarga">
            </div>

            <div class="auth-login-metrics">
                <article>
                    <span class="auth-metric-icon auth-metric-green">
                        <img src="{{ asset('assets/svg/icon-wallet.svg') }}" alt="">
                    </span>
                    <div>
                        <small>Total Saldo</small>
                        <strong>Rp 24.580.000</strong>
                        <em>Naik 8,5% dari bulan lalu</em>
                    </div>
                </article>
                <article>
                    <span class="auth-metric-icon auth-metric-red">
                        <img src="{{ asset('assets/svg/icon-expense.svg') }}" alt="">
                    </span>
                    <div>
                        <small>Pengeluaran Bulan Ini</small>
                        <strong>Rp 11.230.000</strong>
                        <em>Naik 5,6% dari bulan lalu</em>
                    </div>
                </article>
                <article>
                    <span class="auth-metric-icon auth-metric-amber">
                        <img src="{{ asset('assets/svg/icon-budget.svg') }}" alt="">
                    </span>
                    <div>
                        <small>Anggaran Bulan Ini</small>
                        <strong>Rp 7.520.000</strong>
                        <em>32% dari total anggaran</em>
                    </div>
                </article>
            </div>

            <div class="auth-feature-strip">
                <article>
                    <span class="auth-feature-icon auth-feature-green">
                        <img src="{{ asset('assets/svg/icon-shield.svg') }}" alt="">
                    </span>
                    <div>
                        <strong>Aman</strong>
                        <p>Data keluarga Anda dienkripsi dan terlindungi.</p>
                    </div>
                </article>
                <article>
                    <span class="auth-feature-icon auth-feature-blue">
                        <img src="{{ asset('assets/svg/icon-lightning.svg') }}" alt="">
                    </span>
                    <div>
                        <strong>Mudah</strong>
                        <p>Kelola keuangan keluarga kapan saja, di mana saja.</p>
                    </div>
                </article>
                <article>
                    <span class="auth-feature-icon auth-feature-purple">
                        <img src="{{ asset('assets/svg/icon-budget.svg') }}" alt="">
                    </span>
                    <div>
                        <strong>Transparan</strong>
                        <p>Semua transaksi jelas dan bisa dilihat bersama.</p>
                    </div>
                </article>
            </div>

            <div class="auth-trust-row">
                <p><span></span> Dipercaya oleh ribuan keluarga di Indonesia</p>
                <div>
                    <b>B</b><b>S</b><b>R</b><b>A</b><b>D</b>
                    <strong>*****</strong>
                    <span>4.9/5 dari 2.500+ keluarga</span>
                </div>
            </div>
        </section>

        <section class="auth-login-panel">
            <form method="POST" action="{{ route('login.store') }}" class="auth-login-card" x-data="{ showPassword: false }">
                @csrf
                <div class="auth-form-heading">
                    <img src="{{ asset('assets/svg/logo-famfinance.svg') }}" alt="FamFinance">
                    <h2>Masuk ke Akun</h2>
                    <p>Selamat datang kembali! Kelola keuangan keluarga Anda dengan mudah dan aman.</p>
                </div>

                @if ($errors->any())
                    <div class="auth-error-box">{{ $errors->first() }}</div>
                @endif

                <label class="auth-field">
                    <span>Email atau Username</span>
                    <i>
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20 21a8 8 0 0 0-16 0" />
                            <path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                        </svg>
                        <input name="login" value="{{ old('login') }}" placeholder="Masukkan email atau username Anda"
                            autofocus required>
                    </i>
                </label>

                <label class="auth-field">
                    <span>Password</span>
                    <i>
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M7 11V8a5 5 0 0 1 10 0v3" />
                            <path d="M5 11h14v10H5z" />
                        </svg>
                        <input name="password" :type="showPassword ? 'text' : 'password'"
                            placeholder="Masukkan password Anda" required>
                        <button type="button" @click="showPassword = !showPassword" title="Tampilkan password">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z" />
                                <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                            </svg>
                        </button>
                    </i>
                </label>

                <div class="auth-login-options">
                    <label>
                        <input name="remember" value="1" type="checkbox">
                        Ingat saya
                    </label>
                    <span>Lupa password?</span>
                </div>

                <button type="submit" class="auth-submit-button">Masuk</button>

                <div class="auth-divider"><span>atau</span></div>

                <button type="button" class="auth-google-button">
                    <strong>G</strong>
                    Masuk dengan Google
                </button>

                <p class="auth-switch-link">
                    Belum punya akun?
                    <a href="{{ route('register-family') }}">Daftar</a>
                </p>
            </form>
        </section>
    </main>
</body>

</html>
