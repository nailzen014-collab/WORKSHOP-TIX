<x-admin-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow">Panel Panitia</p>
            <h1 class="mt-1 font-display text-2xl font-extrabold uppercase tracking-tight text-forest-900">Daftar Peserta</h1>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-filter-bar :reset="route('admin.participants.index')">
                <div class="w-full sm:w-56">
                    <label for="q" class="text-xs font-semibold uppercase tracking-wide text-forest-600">Cari Peserta</label>
                    <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Nama atau email" class="mt-1 block w-full rounded-xl border-forest-900/20 bg-cream-50 text-sm text-forest-900 shadow-sm focus:border-forest-600 focus:ring-forest-600">
                </div>
                <x-select-field label="Workshop" name="workshop" :value="request('workshop')" :options="$workshops" placeholder="Semua workshop" class="w-full sm:w-56" />
                <x-select-field label="Kategori" name="category" :value="request('category')" :options="$categories" placeholder="Semua kategori" />
                <x-select-field label="Jenis Pembayaran" name="payment_type" :value="request('payment_type')" :options="$paymentTypes" placeholder="Semua jenis" class="w-full sm:w-52" />
                <x-select-field label="Status Pembayaran" name="payment_status" :value="request('payment_status')" :options="$paymentStatuses" placeholder="Semua status" class="w-full sm:w-48" />
            </x-filter-bar>

            <div class="card-cream overflow-hidden">
                @if ($registrations->isEmpty())
                    <p class="px-6 py-8 text-center text-sm text-forest-600">Belum ada peserta terdaftar.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-forest-900/10 text-sm">
                            <thead class="bg-cream-200/50 text-left text-xs uppercase tracking-wide text-forest-600">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Peserta</th>
                                    <th class="px-6 py-3 font-semibold">Workshop</th>
                                    <th class="px-6 py-3 font-semibold">Nominal</th>
                                    <th class="px-6 py-3 font-semibold">Jenis Pembayaran</th>
                                    <th class="px-6 py-3 font-semibold">Status</th>
                                    <th class="px-6 py-3 font-semibold">Tanggal Lunas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-forest-900/10">
                                @foreach ($registrations as $registration)
                                    @php($payment = $registration->payment)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-forest-900">{{ $registration->user?->name ?? $registration->guest_name ?? 'Peserta guest' }}</div>
                                            <div class="text-xs text-forest-500">{{ $registration->user?->email ?? 'Tanpa akun' }}</div>
                                            <ul class="mt-2 space-y-1 text-xs text-forest-700">
                                                @foreach ($registration->attendees ?? [$registration->guest_name ?? $registration->user?->name ?? 'Peserta guest'] as $attendee)
                                                    <li>{{ $loop->iteration }}. {{ $attendee }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-forest-800">{{ $registration->workshop->title }}</div>
                                            <div class="text-xs text-forest-500">{{ $registration->workshop->category?->name ?? 'Tanpa kategori' }}</div>
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-forest-800">Rp{{ number_format($payment?->amount ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-forest-700">{{ $payment?->paymentTypeLabel() ?? '—' }}</td>
                                        <td class="px-6 py-4">
                                            <x-status-badge entity="payment" :code="$payment?->payment_status" fallback="Menunggu" />
                                        </td>
                                        <td class="px-6 py-4 text-forest-700">{{ $payment?->paid_at?->translatedFormat('d M Y H:i') ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            @if ($registrations->hasPages())
                <div>{{ $registrations->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
