<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('workshops.show', $registration->workshop) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-forest-600 hover:text-forest-900">&larr; Kembali ke workshop</a>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <section class="card-cream overflow-hidden">
                <img
                    src="{{ $registration->workshop->image_url ?: asset('images/hero-workshop.png') }}"
                    alt="{{ $registration->workshop->title }}"
                    class="aspect-[16/9] w-full object-cover"
                />

                <div class="p-6 sm:p-8">
                    <p class="eyebrow">Pesanan Guest</p>
                    <h1 class="display-heading mt-2 text-3xl">{{ $registration->workshop->title }}</h1>
                    <p class="mt-2 text-sm text-forest-600">{{ $registration->guest_name }}</p>

                    @if (session('status'))
                        <p class="mt-5 rounded-xl bg-lime-100 p-3 text-sm text-forest-900">{{ session('status') }}</p>
                    @endif
                    @if (session('error'))
                        <p class="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</p>
                    @endif

                    @php
                        $paid = $registration->payment->isPaid();
                    @endphp

                    @if ($paid && $registration->ticket)
                        <div class="mt-7 border-t border-forest-900/10 pt-6">
                            <p class="eyebrow text-forest-600">Tiket Anda</p>
                            <div class="mt-3 rounded-2xl bg-forest-900 p-6 text-center text-cream-50">
                                <p class="text-xs uppercase tracking-widest text-cream-100/60">Kode Tiket</p>
                                <p class="mt-2 break-all font-mono text-2xl font-bold tracking-widest text-lime-500">{{ $registration->ticket->ticket_code }}</p>
                            </div>

                            <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                                <div>
                                    <dt class="text-forest-500">Jadwal</dt>
                                    <dd class="mt-1 font-semibold text-forest-900">{{ $registration->workshop->starts_at->translatedFormat('l, d F Y, H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-forest-500">Lokasi</dt>
                                    <dd class="mt-1 font-semibold text-forest-900">{{ $registration->workshop->location }}</dd>
                                </div>
                            </dl>

                            <p class="mt-5 rounded-xl bg-cream-200/60 p-4 text-sm leading-relaxed text-forest-700">
                                Pembayaran berhasil. Simpan tautan privat ini atau screenshot tiket untuk ditunjukkan kepada panitia saat acara.
                            </p>
                        </div>
                    @elseif ($paid)
                        <p class="mt-6 rounded-xl bg-amber-100 p-4 text-sm text-amber-900">
                            Pembayaran sudah lunas, tetapi tiket belum tersedia. Muat ulang halaman ini beberapa saat lagi.
                        </p>
                    @else
                        <dl class="mt-6 grid gap-4 border-y border-forest-900/10 py-5 sm:grid-cols-2">
                            <div>
                                <dt class="eyebrow">Nomor Pesanan</dt>
                                <dd class="mt-1 font-mono text-sm text-forest-900">{{ $registration->payment->order_id }}</dd>
                            </div>
                            <div>
                                <dt class="eyebrow">Total</dt>
                                <dd class="mt-1 font-display text-xl font-extrabold text-forest-900">Rp{{ number_format($registration->payment->amount, 0, ',', '.') }}</dd>
                            </div>
                        </dl>

                        <div class="mt-5 flex items-center gap-2">
                            <span class="text-sm font-semibold text-forest-700">Status pembayaran:</span>
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800">
                                {{ ucfirst($registration->payment->payment_status) }}
                            </span>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <form method="POST" action="{{ $payUrl }}">
                                @csrf
                                <button type="submit" class="pill-button-lime">Bayar Sekarang</button>
                            </form>
                            <form method="POST" action="{{ $checkUrl }}">
                                @csrf
                                <button type="submit" class="pill-button-outline">Cek Status Pembayaran</button>
                            </form>
                        </div>

                        <p class="mt-5 text-xs leading-relaxed text-forest-600">
                            Tautan halaman ini bersifat privat dan berlaku selama 7 hari. Simpan tautan ini untuk melihat tiket setelah pembayaran terverifikasi.
                        </p>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
