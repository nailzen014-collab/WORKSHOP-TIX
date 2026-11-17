<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-forest-600 hover:text-forest-900">&larr; Kembali ke daftar workshop</a>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[1.6fr_1fr] items-start">
                {{-- Main --}}
                <article class="card-cream overflow-hidden">
                    <img
                        src="{{ $workshop->image_url ?: asset('images/hero-workshop.png') }}"
                        alt="{{ $workshop->title }}"
                        class="aspect-[16/9] w-full object-cover"
                        fetchpriority="high"
                    />

                    <div class="p-6 sm:p-8">
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($workshop->category)
                                <span class="badge-chip">{{ $workshop->category->name }}</span>
                            @endif
                            <span class="badge-chip-lime">{{ $workshop->location }}</span>
                        </div>

                        <h1 class="display-heading mt-4 text-4xl sm:text-5xl">{{ $workshop->title }}</h1>

                        <div class="mt-6 grid gap-5 border-y border-forest-900/10 py-5 sm:grid-cols-2">
                            <div>
                                <p class="eyebrow">Lokasi</p>
                                <p class="mt-1 text-sm font-semibold text-forest-800">{{ $workshop->location }}</p>
                            </div>
                            <div>
                                <p class="eyebrow">Jadwal</p>
                                <p class="mt-1 text-sm font-semibold text-forest-800">{{ $workshop->starts_at->translatedFormat('l, d F Y, H:i') }}</p>
                                @if ($workshop->ends_at)
                                    <p class="mt-1 text-xs text-forest-600">Selesai {{ $workshop->ends_at->translatedFormat('l, d F Y, H:i') }}</p>
                                @endif
                            </div>
                            <div class="sm:col-span-2">
                                <p class="eyebrow">Harga</p>
                                <p class="mt-1 font-display text-2xl font-extrabold text-forest-900">Rp{{ number_format($workshop->price, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h2 class="font-display text-lg font-extrabold uppercase tracking-tight text-forest-900">Tentang Workshop</h2>
                            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-forest-700">{{ $workshop->description }}</p>
                        </div>
                    </div>
                </article>

                {{-- CTA --}}
                <aside class="card-cream p-6 sm:p-8">
                    <p class="eyebrow">Status Anda</p>
                    <div class="mt-3">
                        @auth
                            @if (! $registration)
                                <p class="text-sm text-forest-700">Anda belum terdaftar pada workshop ini.</p>
                                <form method="POST" action="{{ route('registrations.store', $workshop) }}" class="mt-5 space-y-4" data-attendee-form>
                                    @csrf
                                    @include('workshops.attendee-fields')
                                    <button type="submit" class="pill-button-lime w-full justify-center">
                                        Daftar Workshop
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6 4l6 6-6 6V4z" /></svg>
                                    </button>
                                </form>
                            @elseif ($registration->tickets->isNotEmpty())
                                <p class="text-sm font-semibold text-forest-700">Pembayaran lunas. Tiket peserta sudah terbit.</p>
                                <ul class="mt-5 space-y-2">
                                    @foreach ($registration->tickets as $ticket)
                                        <li>
                                            <a href="{{ route('tickets.show', $ticket) }}" class="pill-button w-full justify-center">
                                                Tiket {{ $loop->iteration }} &mdash; {{ $ticket->attendee_name ?? 'Peserta' }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-forest-700">Anda sudah terdaftar. Status pembayaran: <strong>{{ ucfirst($registration->payment?->payment_status ?? 'pending') }}</strong>.</p>
                                <a href="{{ route('payments.show', $registration) }}" class="pill-button mt-5 w-full justify-center">Lanjutkan Pembayaran</a>
                            @endif
                        @else
                            <p class="text-sm text-forest-700">Pilih masuk atau buat akun agar tiket tersimpan di akun Anda, atau lanjut tanpa akun.</p>
                            <div class="mt-5 grid gap-3">
                                <a href="{{ route('login') }}" class="pill-button-lime w-full justify-center">Masuk</a>
                                <a href="{{ route('register') }}" class="pill-button-outline w-full justify-center">Buat Akun</a>
                            </div>

                            <form method="POST" action="{{ route('registrations.guest.store', $workshop) }}" class="mt-6 space-y-4 border-t border-forest-900/10 pt-5" data-attendee-form>
                                @csrf
                                @include('workshops.attendee-fields')
                                <p class="text-xs leading-relaxed text-forest-600">Setelah pembayaran, tiket tampil lewat tautan privat ini. Simpan tautannya atau screenshot tiketnya.</p>
                                <button type="submit" class="pill-button w-full justify-center">Lanjut Tanpa Akun</button>
                            </form>
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
