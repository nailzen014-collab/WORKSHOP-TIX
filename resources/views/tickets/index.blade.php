<x-app-layout>
    <x-slot name="header">
        <h1 class="font-display text-xl font-extrabold uppercase tracking-tight text-forest-900">Tiket Saya</h1>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($registrations->isEmpty())
                <div class="card-cream p-12 text-center">
                    <p class="text-forest-700">Anda belum mendaftar workshop apa pun.</p>
                    <a href="{{ route('home') }}" class="pill-button-lime mt-5">Lihat Workshop</a>
                </div>
            @else
                <ul class="space-y-4">
                    @foreach ($registrations as $registration)
                        @php
                            $paid = $registration->payment?->isPaid() ?? false;
                        @endphp
                        <li class="card-cream p-6">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <h2 class="font-display text-lg font-extrabold uppercase tracking-tight text-forest-900">{{ $registration->workshop->title }}</h2>
                                    <p class="mt-1 text-sm text-forest-600">
                                        {{ $registration->workshop->starts_at->translatedFormat('l, d F Y H:i') }} &middot; {{ $registration->workshop->location }}
                                    </p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $paid ? 'bg-lime-500 text-forest-900' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $paid ? 'Lunas' : ucfirst($registration->payment?->payment_status ?? 'pending') }}
                                </span>
                            </div>

                            <div class="mt-4">
                                @if ($paid && $registration->ticket)
                                    <a href="{{ route('tickets.show', $registration->ticket) }}" class="pill-button">Lihat Tiket</a>
                                @else
                                    <a href="{{ route('payments.show', $registration) }}" class="pill-button-outline">Lanjutkan Pembayaran</a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>
