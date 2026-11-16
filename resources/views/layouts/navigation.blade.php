<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-forest-900/10 bg-cream-50/95 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-10">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-forest-900 text-lime-500">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 8l8-4 8 4-8 4-8-4z" />
                            <path d="M4 8v6l8 4 8-4V8" />
                        </svg>
                    </span>
                    <span class="font-display text-lg font-extrabold uppercase tracking-tight text-forest-900">WorkshopTIX</span>
                </a>

                <!-- Navigation Links -->
                <div class="hidden items-center gap-7 sm:flex">
                    <a href="{{ route('home') }}" class="text-sm font-semibold {{ request()->routeIs('home', 'workshops.*') ? 'text-forest-900' : 'text-forest-600 hover:text-forest-900' }}">Workshop</a>

                    @auth
                        <a href="{{ route('tickets.index') }}" class="text-sm font-semibold {{ request()->routeIs('tickets.*') ? 'text-forest-900' : 'text-forest-600 hover:text-forest-900' }}">Tiket Saya</a>

                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold {{ request()->routeIs('admin.*') ? 'text-forest-900' : 'text-forest-600 hover:text-forest-900' }}">Admin</a>
                        @endif
                    @endauth
                </div>
            </div>

            @auth
                <div class="hidden items-center gap-3 sm:flex">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 rounded-full border border-forest-900/20 px-4 py-2 text-sm font-semibold text-forest-800 hover:border-forest-900">
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-forest-900 text-xs font-bold text-cream-50">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                {{ Auth::user()->name }}
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('tickets.index')">Tiket Saya</x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @else
                <div class="hidden items-center gap-3 sm:flex">
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-forest-700 hover:text-forest-900">Masuk</a>
                    <a href="{{ route('register') }}" class="pill-button-lime">Daftar</a>
                </div>
            @endauth

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-forest-700 hover:bg-cream-200 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-forest-900/10 bg-cream-50 sm:hidden">
        <div class="space-y-1 px-4 py-3">
            <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-forest-800 hover:bg-cream-200">Workshop</a>

            @auth
                <a href="{{ route('tickets.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-forest-800 hover:bg-cream-200">Tiket Saya</a>

                @if (Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-forest-800 hover:bg-cream-200">Admin</a>
                @endif

                <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-forest-800 hover:bg-cream-200">Profile</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm font-semibold text-forest-800 hover:bg-cream-200">Log Out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-forest-800 hover:bg-cream-200">Masuk</a>
                <a href="{{ route('register') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-forest-800 hover:bg-cream-200">Daftar</a>
            @endauth
        </div>
    </div>
</nav>
