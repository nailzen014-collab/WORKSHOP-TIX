<x-app-layout>
    <x-slot name="header">
        <h1 class="font-display text-xl font-extrabold uppercase tracking-tight text-forest-900">Pembayaran</h1>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card-cream p-6 sm:p-8">
                <p class="eyebrow">Workshop</p>
                <h2 class="display-heading mt-2 text-3xl">{{ $registration->workshop->title }}</h2>
                <p class="mt-2 text-sm text-forest-600">
                    {{ $registration->workshop->starts_at->translatedFormat('l, d F Y H:i') }} &middot; {{ $registration->workshop->location }}
                </p>

                <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-cream-200/60 p-4">
                        <dt class="eyebrow">Jumlah Tiket</dt>
                        <dd class="mt-1 text-sm font-semibold text-forest-900">{{ count($registration->attendees ?? [$registration->user?->name ?? 'Peserta']) }} tiket</dd>
                    </div>
                    <div class="rounded-2xl bg-cream-200/60 p-4">
                        <dt class="eyebrow">Kode Pesanan</dt>
                        <dd class="mt-1 font-mono text-sm text-forest-900">{{ $registration->payment->order_id }}</dd>
                    </div>
                    <div class="rounded-2xl bg-cream-200/60 p-4">
                        <dt class="eyebrow">Nominal</dt>
                        <dd class="mt-1 font-display text-2xl font-extrabold text-forest-900">Rp{{ number_format($registration->payment->amount, 0, ',', '.') }}</dd>
                    </div>
                </dl>

                <div class="mt-5">
                    <p class="eyebrow">Peserta per tiket</p>
                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-forest-700">
                        @foreach ($registration->attendees ?? [$registration->user?->name ?? 'Peserta'] as $attendee)
                            <li>{{ $attendee }}</li>
                        @endforeach
                    </ul>
                </div>

                @php
                    $paid = $registration->payment->isPaid();
                @endphp

                <div class="mt-5 flex items-center gap-2">
                    <span class="text-sm font-semibold text-forest-700">Status pembayaran:</span>
                    <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $paid ? 'bg-lime-500 text-forest-900' : 'bg-amber-100 text-amber-800' }}">
                        {{ ucfirst($registration->payment->payment_status) }}
                    </span>
                </div>

                <div class="mt-8 flex flex-wrap gap-3 border-t border-forest-900/10 pt-6">
                    @if ($paid)
                        @if ($registration->tickets->isNotEmpty())
                            @foreach ($registration->tickets as $ticket)
                                <a href="{{ route('tickets.show', $ticket) }}" class="pill-button">Tiket {{ $loop->iteration }}: {{ $ticket->attendee_name ?? 'Peserta' }}</a>
                            @endforeach
                        @endif
                    @else
                        <form method="POST" action="{{ route('payments.pay', $registration) }}">
                            @csrf
                            <button type="submit" class="pill-button-lime">
                                {{ $registration->payment->snap_token ? 'Buka Halaman Bayar' : 'Bayar Sekarang' }}
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6 4l6 6-6 6V4z" /></svg>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('payments.check', $registration) }}">
                            @csrf
                            <button type="submit" class="pill-button-outline">Cek Status Pembayaran</button>
                        </form>
                    @endif
                </div>

                @unless ($paid)
                    <p class="mt-4 rounded-2xl bg-cream-200/50 p-4 text-xs leading-relaxed text-forest-600">
                        Setelah pembayaran selesai, Midtrans akan mengarahkan Anda kembali ke halaman ini. Jika status belum berubah, tekan "Cek Status Pembayaran".
                        Saat dijalankan secara lokal, notifikasi otomatis dari Midtrans tidak dapat menjangkau server Anda, sehingga pengecekan manual mungkin tetap diperlukan.
                    </p>
                @endunless
            </div>
        </div>
    </div>
</x-app-layout>
