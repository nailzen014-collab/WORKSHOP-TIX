<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Master Data</p>
                <h1 class="mt-1 font-display text-2xl font-extrabold uppercase tracking-tight text-forest-900">Jenis Pembayaran</h1>
            </div>
            <a href="{{ route('admin.payment-types.create') }}" class="pill-button-lime">+ Jenis Pembayaran</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-filter-bar :reset="route('admin.payment-types.index')">
                <div class="w-full sm:w-64">
                    <label for="q" class="text-xs font-semibold uppercase tracking-wide text-forest-600">Cari</label>
                    <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Nama atau kode" class="mt-1 block w-full rounded-xl border-forest-900/20 bg-cream-50 text-sm text-forest-900 shadow-sm focus:border-forest-600 focus:ring-forest-600">
                </div>
            </x-filter-bar>

            <div class="card-cream overflow-hidden">
                @if ($paymentTypes->isEmpty())
                    <p class="px-6 py-8 text-center text-sm text-forest-600">Belum ada jenis pembayaran.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-forest-900/10 text-sm">
                            <thead class="bg-cream-200/50 text-left text-xs uppercase tracking-wide text-forest-600">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Jenis</th>
                                    <th class="px-6 py-3 font-semibold">Kode Midtrans</th>
                                    <th class="px-6 py-3 font-semibold">Grup</th>
                                    <th class="px-6 py-3 font-semibold">Pembayaran</th>
                                    <th class="px-6 py-3 font-semibold">Status</th>
                                    <th class="px-6 py-3 text-right font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-forest-900/10">
                                @foreach ($paymentTypes as $paymentType)
                                    <tr>
                                        <td class="px-6 py-4 font-semibold text-forest-900">{{ $paymentType->name }}</td>
                                        <td class="px-6 py-4 font-mono text-xs text-forest-600">{{ $paymentType->code }}</td>
                                        <td class="px-6 py-4 text-forest-700">{{ $paymentType->group ?? '-' }}</td>
                                        <td class="px-6 py-4 text-forest-700">{{ $paymentType->payments_count }}</td>
                                        <td class="px-6 py-4">
                                            <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $paymentType->is_active ? 'bg-lime-500 text-forest-900' : 'bg-cream-200 text-forest-600' }}">
                                                {{ $paymentType->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('admin.payment-types.edit', $paymentType) }}" class="text-sm font-semibold text-forest-600 hover:text-forest-900">Edit</a>
                                                <form method="POST" action="{{ route('admin.payment-types.destroy', $paymentType) }}" onsubmit="return confirm('Hapus jenis pembayaran ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-800">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            @if ($paymentTypes->hasPages())
                <div>{{ $paymentTypes->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
