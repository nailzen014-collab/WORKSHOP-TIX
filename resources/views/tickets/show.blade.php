<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-forest-600 hover:text-forest-900">&larr; Kembali ke Tiket Saya</a>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-4xl border border-forest-900/10 bg-cream-50 shadow-card">
                <div class="bg-forest-900 p-6 text-center text-cream-50">
                    <p class="text-xs uppercase tracking-[0.3em] text-lime-500">E-Ticket</p>
                    <h1 class="mt-1 font-display text-2xl font-extrabold uppercase tracking-tight">WorkshopTIX</h1>
                </div>

                <div class="p-6 sm:p-8">
                    <h2 class="display-heading text-2xl">{{ $ticket->registration->workshop->title }}</h2>

                    <div class="mt-6 rounded-2xl bg-forest-900 py-4 text-center text-cream-50">
                        <p class="text-xs uppercase tracking-widest text-cream-100/60">Kode Tiket</p>
                        <p class="mt-1 font-mono text-2xl font-bold tracking-widest text-lime-500">{{ $ticket->ticket_code }}</p>
                    </div>

                    <dl class="mt-6 space-y-3 text-sm">
                        <div class="flex justify-between gap-4 border-b border-dashed border-forest-900/15 pb-3">
                            <dt class="text-forest-500">Peserta</dt>
                            <dd class="font-semibold text-forest-900">{{ $ticket->registration->user->name }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-dashed border-forest-900/15 pb-3">
                            <dt class="text-forest-500">Jadwal</dt>
                            <dd class="text-right font-semibold text-forest-900">{{ $ticket->registration->workshop->starts_at->translatedFormat('l, d F Y H:i') }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-dashed border-forest-900/15 pb-3">
                            <dt class="text-forest-500">Lokasi</dt>
                            <dd class="font-semibold text-forest-900">{{ $ticket->registration->workshop->location }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-forest-500">Diterbitkan</dt>
                            <dd class="font-semibold text-forest-900">{{ $ticket->issued_at->translatedFormat('d M Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="border-t border-dashed border-forest-900/15 bg-cream-200/50 p-4 text-center text-xs text-forest-600">
                    Tunjukkan tiket ini kepada panitia saat hari acara.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
