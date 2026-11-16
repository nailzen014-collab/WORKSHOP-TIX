<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WorkshopTIX') }} &middot; Admin</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=archivo:500,600,700,800,900|figtree:400,500,600,700|fraunces:400,500,600i" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-cream-100">
            <div
                x-cloak
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-forest-900/50 lg:hidden"
                @click="sidebarOpen = false"
            ></div>

            <aside
                x-cloak
                class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-forest-900 text-cream-50 shadow-card transition-transform duration-200 lg:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <div class="flex h-16 shrink-0 items-center gap-2 border-b border-cream-50/10 px-5">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-lime-500 text-forest-900">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 8l8-4 8 4-8 4-8-4z" />
                                <path d="M4 8v6l8 4 8-4V8" />
                            </svg>
                        </span>
                        <span class="font-display text-lg font-extrabold uppercase tracking-tight">WorkshopTIX</span>
                    </a>
                    <button type="button" class="ml-auto rounded-full p-2 text-cream-100/70 hover:bg-forest-800 hover:text-cream-50 lg:hidden" @click="sidebarOpen = false" aria-label="Tutup menu">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                </div>

                @php
                    $primaryNav = [
                        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard'],
                        ['label' => 'Workshop', 'route' => 'admin.workshops.index', 'pattern' => 'admin.workshops.*'],
                        ['label' => 'Peserta', 'route' => 'admin.participants.index', 'pattern' => 'admin.participants.*'],
                    ];

                    $masterNav = [
                        ['label' => 'Kategori', 'route' => 'admin.categories.index', 'pattern' => 'admin.categories.*'],
                        ['label' => 'Jenis Pembayaran', 'route' => 'admin.payment-types.index', 'pattern' => 'admin.payment-types.*'],
                        ['label' => 'Status', 'route' => 'admin.statuses.index', 'pattern' => 'admin.statuses.*'],
                    ];
                @endphp

                <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5">
                    <p class="px-3 pb-2 text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-cream-100/50">Panel Panitia</p>

                    @foreach ($primaryNav as $item)
                        @php($active = request()->routeIs($item['pattern']))
                        <a
                            href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 rounded-full px-3 py-2.5 text-sm font-semibold transition-colors {{ $active ? 'bg-lime-500 text-forest-900' : 'text-cream-100/70 hover:bg-forest-800 hover:text-cream-50' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $active ? 'bg-forest-900' : 'bg-cream-100/30' }}"></span>
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    <p class="px-3 pb-2 pt-5 text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-cream-100/50">Master Data</p>

                    @foreach ($masterNav as $item)
                        @php($active = request()->routeIs($item['pattern']))
                        <a
                            href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 rounded-full px-3 py-2.5 text-sm font-semibold transition-colors {{ $active ? 'bg-lime-500 text-forest-900' : 'text-cream-100/70 hover:bg-forest-800 hover:text-cream-50' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $active ? 'bg-forest-900' : 'bg-cream-100/30' }}"></span>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="shrink-0 border-t border-cream-50/10 p-4">
                    <div class="mb-3 flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-cream-50 text-sm font-bold text-forest-900">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-cream-100/60">Administrator</p>
                        </div>
                    </div>

                    <a href="{{ route('home') }}" class="flex items-center justify-between rounded-full border border-cream-50/20 px-3 py-2 text-xs font-semibold text-cream-100/80 hover:border-cream-50/40 hover:text-cream-50">
                        Lihat Situs
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M6 4l6 6-6 6V4z" /></svg>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full rounded-full px-3 py-2 text-left text-xs font-semibold text-cream-100/60 hover:bg-forest-800 hover:text-cream-50">Log Out</button>
                    </form>
                </div>
            </aside>

            <div class="lg:pl-64">
                <header class="sticky top-0 z-30 border-b border-forest-900/10 bg-cream-50/95 backdrop-blur">
                    <div class="flex min-h-16 items-center gap-4 px-4 py-3 sm:px-6 lg:px-8">
                        <button type="button" class="rounded-full p-2 text-forest-700 hover:bg-cream-200 lg:hidden" @click="sidebarOpen = true" aria-label="Buka menu">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>

                        <div class="min-w-0 flex-1">
                            @isset($header)
                                {{ $header }}
                            @else
                                <p class="eyebrow">Panel Panitia</p>
                            @endisset
                        </div>

                        <a href="{{ route('home') }}" class="hidden items-center gap-1.5 rounded-full border border-forest-900/20 px-4 py-2 text-xs font-semibold text-forest-700 hover:border-forest-900 hover:text-forest-900 sm:inline-flex">
                            Lihat Situs
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M6 4l6 6-6 6V4z" /></svg>
                        </a>
                    </div>
                </header>

                <main class="pb-10">
                    @if (session('status') || session('error'))
                        <div class="px-4 pt-6 sm:px-6 lg:px-8">
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

                <footer class="border-t border-forest-900/10 px-4 py-6 text-xs text-forest-600 sm:px-6 lg:px-8">
                    WorkshopTIX Admin &middot; SMK PESAT
                </footer>
            </div>
        </div>
    </body>
</html>
