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
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-cream-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="border-b border-forest-900/10 bg-cream-50">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @if (session('status') || session('error'))
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                        @if (session('status'))
                            <div class="rounded-2xl border border-forest-600/30 bg-forest-50 px-4 py-3 text-sm text-forest-700" role="status">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="rounded-2xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                @endif

                {{ $slot }}
            </main>

            <footer class="mt-16 border-t border-forest-900/10 bg-forest-900 text-cream-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <p class="font-display text-lg font-extrabold uppercase tracking-tight">WorkshopTIX</p>
                    <p class="text-sm text-cream-100/70">Pendaftaran &amp; Tiket Digital Workshop IT &middot; SMK PESAT</p>
                </div>
            </footer>
        </div>
    </body>
</html>
