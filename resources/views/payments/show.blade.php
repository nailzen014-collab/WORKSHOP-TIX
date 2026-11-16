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
                        <dt class="eyebrow">Kode Pesanan</dt>
                        <dd class="mt-1 font-mono text-sm text-forest-900">{{ $registration->payment->order_id }}</dd>
                    </div>
                    <div class="rounded-2xl bg-cream-200/60 p-4">
                        <dt class="eyebrow">Nominal</dt>
                        <dd class="mt-1 font-display text-2xl font-extrabold text-forest-900">Rp{{ number_format($registration->payment->amount, 0, ',', '.') }}</dd>
                    </div>
                </dl>

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
                        @if ($registration->ticket)
                            <a href="{{ route('tickets.show', $registration->ticket) }}" class="pill-button">Lihat Tiket Saya</a>
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
                        Setelah menyelesaikan pembayaran di halaman Midtrans, kembali ke halaman ini dan tekan "Cek Status Pembayaran".
                        Saat dijalankan secara lokal, notifikasi otomatis dari Midtrans tidak dapat menjangkau server Anda, sehingga pengecekan manual diperlukan.
                    </p>
                @endunless
            </div>
        </div>
    </div>
</x-app-layout>
