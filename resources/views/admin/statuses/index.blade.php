<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Master Data</p>
                <h1 class="mt-1 font-display text-2xl font-extrabold uppercase tracking-tight text-forest-900">Status</h1>
            </div>
            @if ($selectedEntity !== \App\Models\Status::ENTITY_USER_ROLE)
                <a href="{{ route('admin.statuses.create', ['entity' => $selectedEntity]) }}" class="pill-button-lime">+ Status</a>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-filter-bar :reset="route('admin.statuses.index')">
                <x-select-field label="Entitas" name="entity" :value="$selectedEntity" :options="$entities" placeholder="Semua entitas" />
            </x-filter-bar>

            <div class="card-cream overflow-hidden">
                @if ($statuses->isEmpty())
                    <p class="px-6 py-8 text-center text-sm text-forest-600">Belum ada status.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-forest-900/10 text-sm">
                            <thead class="bg-cream-200/50 text-left text-xs uppercase tracking-wide text-forest-600">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Entitas</th>
                                    <th class="px-6 py-3 font-semibold">Kode</th>
                                    <th class="px-6 py-3 font-semibold">Label</th>
                                    <th class="px-6 py-3 font-semibold">Warna</th>
                                    <th class="px-6 py-3 font-semibold">Status</th>
                                    <th class="px-6 py-3 text-right font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-forest-900/10">
                                @foreach ($statuses as $status)
                                    @php
                                        $colorClass = match ($status->color) {
                                            'lime' => 'bg-lime-500 text-forest-900',
                                            'amber' => 'bg-amber-100 text-amber-800',
                                            'red' => 'bg-red-100 text-red-800',
                                            'forest' => 'bg-forest-900 text-cream-50',
                                            default => 'bg-cream-200 text-forest-600',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 text-forest-700">{{ $entities[$status->entity] ?? $status->entity }}</td>
                                        <td class="px-6 py-4 font-mono text-xs text-forest-600">{{ $status->code }}</td>
                                        <td class="px-6 py-4 font-semibold text-forest-900">{{ $status->name }}</td>
                                        <td class="px-6 py-4">
                                            <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $colorClass }}">
                                                {{ ucfirst($status->color ?? 'netral') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $status->is_active ? 'bg-lime-500 text-forest-900' : 'bg-cream-200 text-forest-600' }}">
                                                {{ $status->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($status->entity === \App\Models\Status::ENTITY_USER_ROLE)
                                                <div class="text-right text-xs font-semibold uppercase tracking-wide text-forest-500">Read-only</div>
                                            @else
                                                <div class="flex items-center justify-end gap-3">
                                                    <a href="{{ route('admin.statuses.edit', $status) }}" class="text-sm font-semibold text-forest-600 hover:text-forest-900">Edit</a>
                                                    <form method="POST" action="{{ route('admin.statuses.destroy', $status) }}" onsubmit="return confirm('Hapus status ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-800">Hapus</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            @if ($statuses->hasPages())
                <div>{{ $statuses->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
