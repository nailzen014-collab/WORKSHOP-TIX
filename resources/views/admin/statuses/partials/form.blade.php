@php
    $field = 'mt-1 block w-full rounded-xl border-forest-900/20 bg-cream-50 text-forest-900 shadow-sm focus:border-forest-600 focus:ring-forest-600';
    $label = 'text-xs font-semibold uppercase tracking-wide text-forest-600';
    $isEdit = $status->exists;
    $colors = [
        'lime' => 'Lime',
        'amber' => 'Amber',
        'red' => 'Merah',
        'forest' => 'Forest',
        'neutral' => 'Netral',
    ];
@endphp

<div class="space-y-5">
    @if ($isEdit)
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <p class="{{ $label }}">Entitas</p>
                <p class="mt-1 rounded-xl border border-forest-900/10 bg-cream-200/60 px-4 py-2.5 text-sm font-semibold text-forest-800">{{ $entities[$status->entity] ?? $status->entity }}</p>
            </div>
            <div>
                <p class="{{ $label }}">Kode</p>
                <p class="mt-1 rounded-xl border border-forest-900/10 bg-cream-200/60 px-4 py-2.5 font-mono text-sm text-forest-800">{{ $status->code }}</p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="entity" class="{{ $label }}">Entitas</label>
                <select id="entity" name="entity" class="{{ $field }}" required>
                    @foreach ($entities as $value => $entityLabel)
                        @continue($value === \App\Models\Status::ENTITY_USER_ROLE)
                        <option value="{{ $value }}" @selected(old('entity', $status->entity) === $value)>{{ $entityLabel }}</option>
                    @endforeach
                </select>
                @error('entity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="code" class="{{ $label }}">Kode</label>
                <input id="code" name="code" type="text" class="{{ $field }}" value="{{ old('code', $status->code) }}" placeholder="contoh: published" required>
                @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    @endif

    <div>
        <label for="name" class="{{ $label }}">Label</label>
        <input id="name" name="name" type="text" class="{{ $field }}" value="{{ old('name', $status->name) }}" required>
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="color" class="{{ $label }}">Warna Badge</label>
        <select id="color" name="color" class="{{ $field }}">
            @foreach ($colors as $value => $colorLabel)
                <option value="{{ $value }}" @selected(old('color', $status->color ?? 'neutral') === $value)>{{ $colorLabel }}</option>
            @endforeach
        </select>
        @error('color') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <label for="sort_order" class="{{ $label }}">Urutan</label>
            <input id="sort_order" name="sort_order" type="number" min="0" step="1" class="{{ $field }}" value="{{ old('sort_order', $status->sort_order ?? 0) }}">
            @error('sort_order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-end pb-3">
            <label class="inline-flex items-center gap-2 text-sm font-semibold text-forest-800">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $status->is_active ?? true)) class="rounded border-forest-900/20 text-forest-600 focus:ring-forest-600">
                Status aktif
            </label>
        </div>
    </div>
</div>
