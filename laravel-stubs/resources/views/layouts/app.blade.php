<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'FamFinance')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-950 antialiased" x-data>
    <div class="min-h-screen flex">
        @include('partials.sidebar')
        <main class="flex-1 min-w-0">
            @include('partials.header')
            <section class="p-6 lg:p-8">
                @yield('content')
            </section>
        </main>
    </div>
</body>

</html>
