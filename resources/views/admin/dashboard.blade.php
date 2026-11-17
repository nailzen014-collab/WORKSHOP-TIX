<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Panel Panitia</p>
                <h1 class="mt-1 font-display text-2xl font-extrabold uppercase tracking-tight text-forest-900">Dashboard Admin</h1>
            </div>
            <a href="{{ route('admin.workshops.create') }}" class="pill-button-lime">+ Workshop</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['label' => 'Total Workshop', 'value' => $stats['workshops']],
                    ['label' => 'Total Pendaftaran', 'value' => $stats['registrations']],
                    ['label' => 'Pembayaran Lunas', 'value' => $stats['paid']],
                    ['label' => 'Menunggu Pembayaran', 'value' => $stats['pending']],
                ] as $card)
                    <div class="card-cream p-5">
                        <p class="eyebrow">{{ $card['label'] }}</p>
                        <p class="mt-2 font-display text-3xl font-extrabold text-forest-900">{{ $card['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="card-cream overflow-hidden">
                <div class="flex items-center justify-between border-b border-forest-900/10 px-6 py-4">
                    <h2 class="font-display text-lg font-extrabold uppercase tracking-tight text-forest-900">Pendaftaran Terbaru</h2>
                    <a href="{{ route('admin.participants.index') }}" class="text-sm font-semibold text-forest-600 hover:text-forest-900">Lihat semua</a>
                </div>

                @if ($latestRegistrations->isEmpty())
                    <p class="px-6 py-8 text-center text-sm text-forest-600">Belum ada pendaftaran.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-forest-900/10 text-sm">
                            <thead class="bg-cream-200/50 text-left text-xs uppercase tracking-wide text-forest-600">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Peserta</th>
                                    <th class="px-6 py-3 font-semibold">Workshop</th>
                                    <th class="px-6 py-3 font-semibold">Nominal</th>
                                    <th class="px-6 py-3 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-forest-900/10">
                                @foreach ($latestRegistrations as $registration)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-forest-900">{{ $registration->user?->name ?? $registration->guest_name ?? 'Peserta guest' }}</div>
                                            <div class="text-xs text-forest-500">{{ $registration->user?->email ?? 'Tanpa akun' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-forest-700">{{ $registration->workshop->title }}</td>
                                        <td class="px-6 py-4 font-semibold text-forest-800">
                                            Rp{{ number_format($registration->payment?->amount ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <x-status-badge entity="payment" :code="$registration->payment?->payment_status" fallback="Menunggu" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
