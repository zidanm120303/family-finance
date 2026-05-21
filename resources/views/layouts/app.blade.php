<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'FamFinance')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="bg-slate-50 text-slate-950 antialiased"
    x-data="{
        mobileMenuOpen: false,
        openMobileMenu() { this.mobileMenuOpen = true },
        closeMobileMenu() { this.mobileMenuOpen = false },
    }"
    x-effect="document.documentElement.classList.toggle('overflow-hidden', mobileMenuOpen); document.body.classList.toggle('overflow-hidden', mobileMenuOpen)"
    @keydown.escape.window="closeMobileMenu()"
    @resize.window="if (window.innerWidth >= 1024) closeMobileMenu()"
>
<div class="app-shell min-h-screen flex">
    @include('partials.sidebar')
    <main class="flex-1 min-w-0 overflow-x-hidden">
        @include('partials.header')
        <section class="app-content p-4 pb-24 sm:p-5 sm:pb-24 lg:p-6">
            @if(session('success'))
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif
            @yield('content')
        </section>
    </main>
    @include('partials.mobile-navigation')
</div>
@stack('scripts')
</body>
</html>
