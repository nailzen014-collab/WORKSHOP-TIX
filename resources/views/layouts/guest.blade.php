<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WorkshopTIX') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=archivo:500,600,700,800,900|figtree:400,500,600,700|fraunces:400,500,600i" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-forest-900 antialiased">
        <div class="min-h-screen grid lg:grid-cols-2">
            <div class="hidden flex-col justify-between bg-forest-900 p-12 text-cream-50 lg:flex">
                <a href="/" class="flex items-center gap-2">
                    <span class="grid h-10 w-10 place-items-center rounded-full bg-lime-500 text-forest-900">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 8l8-4 8 4-8 4-8-4z" />
                            <path d="M4 8v6l8 4 8-4V8" />
                        </svg>
                    </span>
                    <span class="font-display text-xl font-extrabold uppercase tracking-tight">WorkshopTIX</span>
                </a>

                <div>
                    <p class="eyebrow text-lime-500">Workshop IT Berbayar</p>
                    <h2 class="display-heading mt-3 text-5xl text-cream-50">
                        Belajar.<br>
                        Terhubung.<br>
                        <span class="accent-serif text-lime-500">Berkembang.</span>
                    </h2>
                    <p class="mt-4 max-w-sm text-sm text-cream-100/70">Daftar workshop IT, selesaikan pembayaran, dan dapatkan tiket digital Anda dalam satu alur.</p>
                </div>

                <p class="text-xs uppercase tracking-[0.2em] text-cream-100/50">SMK PESAT &middot; Rekayasa Perangkat Lunak</p>
            </div>

            <div class="flex flex-col items-center justify-center px-4 py-12 sm:px-8">
                <a href="/" class="mb-8 lg:hidden">
                    <span class="font-display text-xl font-extrabold uppercase tracking-tight text-forest-900">WorkshopTIX</span>
                </a>

                <div class="w-full sm:max-w-md">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
