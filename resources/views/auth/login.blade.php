<x-guest-layout>
    <div class="card-cream p-6 sm:p-8">
        <p class="eyebrow">Selamat Datang Kembali</p>
        <h1 class="display-heading mt-2 text-3xl">Masuk</h1>
        <p class="mt-2 text-sm text-forest-600">Kelola pendaftaran dan tiket workshop Anda.</p>

        <x-auth-session-status class="mt-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-forest-900/20 text-forest-700 shadow-sm focus:ring-forest-600" name="remember">
                <span class="ms-2 text-sm text-forest-600">Ingat saya</span>
            </label>

            <div class="flex items-center justify-between pt-2">
                @if (Route::has('password.request'))
                    <a class="text-sm font-semibold text-forest-600 hover:text-forest-900" href="{{ route('password.request') }}">Lupa password?</a>
                @endif

                <x-primary-button>
                    Masuk
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6 4l6 6-6 6V4z" /></svg>
                </x-primary-button>
            </div>
        </form>

        <p class="mt-6 border-t border-forest-900/10 pt-4 text-center text-sm text-forest-600">
            Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-forest-900 underline">Daftar sekarang</a>
        </p>
    </div>
</x-guest-layout>
