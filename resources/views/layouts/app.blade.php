<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Event Global - Créez et découvrez concerts, compétitions, festivals et évènements au Togo.">
    <meta name="keywords" content="événement, concert, compétition, festival, Lomé, Togo, billetterie">
    <meta name="author" content="Event Global">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:title" content="@yield('title', 'Event Global - Concerts, compétitions & évènements')">
    <meta property="og:description" content="Créez et découvrez les meilleurs évènements : concerts, compétitions, festivals et plus.">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">

    <title>@yield('title', 'Event Global - Concerts, compétitions & évènements')</title>

    <meta name="theme-color" content="#4f46e5">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800,900" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                50: '#f0f7ff', 100: '#e0effe', 200: '#bae0fd', 300: '#7cc2fb',
                                400: '#389ff7', 500: '#004aad', 600: '#0a79eb', 700: '#0961c0',
                                800: '#0d519b', 900: '#114481', 950: '#0b2b54',
                            },
                        }
                    }
                }
            };
        </script>
    @endif

    @include('partials.dark-mode-styles')
    @include('partials.head-scripts')

    @stack('styles')
</head>
<body class="bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 dark:text-slate-100 min-h-screen transition-colors duration-300">
    <x-flash-messages />
    @include('partials.confetti')

    @include('partials.header')

    <main class="min-h-[calc(100vh-80px-300px)]">
        @yield('content')
    </main>

    @include('partials.footer')

    @auth
        <a href="{{ route('events.create') }}"
           class="lg:hidden fixed bottom-6 right-6 z-40 inline-flex items-center justify-center w-14 h-14 rounded-full bg-indigo-600 text-white shadow-lg shadow-indigo-500/40 hover:bg-indigo-700 active:scale-95 transition-all"
           aria-label="Créer un évènement">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
        </a>
    @endauth

    @stack('scripts')
</body>
</html>
