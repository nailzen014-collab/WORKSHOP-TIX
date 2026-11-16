<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-forest-600 hover:text-forest-900">&larr; Kembali ke daftar workshop</a>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[1.6fr_1fr] items-start">
                {{-- Main --}}
                <article class="card-cream p-6 sm:p-8">
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($workshop->category)
                            <span class="badge-chip">{{ $workshop->category->name }}</span>
                        @endif
                        <span class="badge-chip-lime">{{ $workshop->location }}</span>
                        <span class="badge-chip">{{ $workshop->starts_at->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <h1 class="display-heading mt-4 text-4xl sm:text-5xl">{{ $workshop->title }}</h1>

                    <div class="mt-6 flex flex-wrap items-center gap-6 border-y border-forest-900/10 py-4">
                        <div>
                            <p class="eyebrow">Harga</p>
                            <p class="mt-0.5 font-display text-2xl font-extrabold text-forest-900">Rp{{ number_format($workshop->price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="eyebrow">Mulai</p>
                            <p class="mt-0.5 text-sm font-semibold text-forest-800">{{ $workshop->starts_at->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <p class="eyebrow">Selesai</p>
                            <p class="mt-0.5 text-sm font-semibold text-forest-800">{{ $workshop->ends_at ? $workshop->ends_at->translatedFormat('d M Y, H:i') : '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h2 class="font-display text-lg font-extrabold uppercase tracking-tight text-forest-900">Deskripsi</h2>
                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-forest-700">{{ $workshop->description }}</p>
                    </div>
                </article>

                {{-- CTA --}}
                <aside class="card-cream p-6 sm:p-8">
                    <p class="eyebrow">Status Anda</p>
                    <div class="mt-3">
                        @auth
                            @if (! $registration)
                                <p class="text-sm text-forest-700">Anda belum terdaftar pada workshop ini.</p>
                                <form method="POST" action="{{ route('registrations.store', $workshop) }}" class="mt-5">
                                    @csrf
                                    <button type="submit" class="pill-button-lime w-full justify-center">
                                        Daftar Workshop
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6 4l6 6-6 6V4z" /></svg>
                                    </button>
                                </form>
                            @elseif ($registration->ticket)
                                <p class="text-sm font-semibold text-forest-700">Pembayaran lunas. Tiket Anda sudah terbit.</p>
                                <a href="{{ route('tickets.show', $registration->ticket) }}" class="pill-button mt-5 w-full justify-center">Lihat Tiket</a>
                            @else
                                <p class="text-sm text-forest-700">Anda sudah terdaftar. Status pembayaran: <strong>{{ ucfirst($registration->payment?->payment_status ?? 'pending') }}</strong>.</p>
                                <a href="{{ route('payments.show', $registration) }}" class="pill-button mt-5 w-full justify-center">Lanjutkan Pembayaran</a>
                            @endif
                        @else
                            <p class="text-sm text-forest-700">Masuk atau daftar akun untuk mengikuti workshop ini.</p>
                            <a href="{{ route('login') }}" class="pill-button-lime mt-5 w-full justify-center">Masuk untuk Daftar</a>
                        @endauth
                    </div>

                    <ul class="mt-6 space-y-2 border-t border-forest-900/10 pt-4 text-sm text-forest-600">
                        <li class="flex items-center gap-2"><span class="text-forest-900">&#10003;</span> Pembayaran aman via Midtrans</li>
                        <li class="flex items-center gap-2"><span class="text-forest-900">&#10003;</span> Tiket digital setelah lunas</li>
                        <li class="flex items-center gap-2"><span class="text-forest-900">&#10003;</span> Pencegahan pendaftaran ganda</li>
                    </ul>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
