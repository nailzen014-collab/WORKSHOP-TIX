<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Panel Panitia</p>
                <h1 class="mt-1 font-display text-2xl font-extrabold uppercase tracking-tight text-forest-900">Manajemen Workshop</h1>
            </div>
            <a href="{{ route('admin.workshops.create') }}" class="pill-button-lime">+ Workshop</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-filter-bar :reset="route('admin.workshops.index')">
                <div class="w-full sm:w-56">
                    <label for="q" class="text-xs font-semibold uppercase tracking-wide text-forest-600">Cari</label>
                    <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Judul atau lokasi" class="mt-1 block w-full rounded-xl border-forest-900/20 bg-cream-50 text-sm text-forest-900 shadow-sm focus:border-forest-600 focus:ring-forest-600">
                </div>
                <x-select-field label="Status" name="status" :value="request('status')" :options="$statuses" placeholder="Semua status" />
                <x-select-field label="Kategori" name="category" :value="request('category')" :options="$categories" placeholder="Semua kategori" />
            </x-filter-bar>

            <div class="card-cream overflow-hidden">
                @if ($workshops->isEmpty())
                    <p class="px-6 py-8 text-center text-sm text-forest-600">Belum ada workshop.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-forest-900/10 text-sm">
                            <thead class="bg-cream-200/50 text-left text-xs uppercase tracking-wide text-forest-600">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Judul</th>
                                    <th class="px-6 py-3 font-semibold">Kategori</th>
                                    <th class="px-6 py-3 font-semibold">Jadwal</th>
                                    <th class="px-6 py-3 font-semibold">Harga</th>
                                    <th class="px-6 py-3 font-semibold">Pesanan</th>
                                    <th class="px-6 py-3 font-semibold">Status</th>
                                    <th class="px-6 py-3 text-right font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-forest-900/10">
                                @foreach ($workshops as $workshop)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-forest-900">{{ $workshop->title }}</div>
                                            <div class="text-xs text-forest-500">{{ $workshop->location }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-forest-700">{{ $workshop->category?->name ?? '—' }}</td>
                                        <td class="px-6 py-4 text-forest-700">{{ $workshop->starts_at->translatedFormat('d M Y H:i') }}</td>
                                        <td class="px-6 py-4 font-semibold text-forest-800">Rp{{ number_format($workshop->price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-forest-700">{{ $workshop->registrations_count }}</td>
                                        <td class="px-6 py-4">
                                            <x-status-badge entity="workshop" :code="$workshop->status" />
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('admin.workshops.edit', $workshop) }}" class="text-sm font-semibold text-forest-600 hover:text-forest-900">Edit</a>
                                                <form method="POST" action="{{ route('admin.workshops.destroy', $workshop) }}" onsubmit="return confirm('Hapus workshop ini?');">
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

            @if ($workshops->hasPages())
                <div class="mt-6">{{ $workshops->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
