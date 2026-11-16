@php
    $field = 'mt-1 block w-full rounded-xl border-forest-900/20 bg-cream-50 text-forest-900 shadow-sm focus:border-forest-600 focus:ring-forest-600';
    $label = 'text-xs font-semibold uppercase tracking-wide text-forest-600';
@endphp

<div class="space-y-5">
    <div>
        <label for="name" class="{{ $label }}">Nama Kategori</label>
        <input id="name" name="name" type="text" class="{{ $field }}" value="{{ old('name', $category->name) }}" required>
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="slug" class="{{ $label }}">Slug (opsional)</label>
        <input id="slug" name="slug" type="text" class="{{ $field }}" value="{{ old('slug', $category->slug) }}" placeholder="otomatis dari nama">
        @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="{{ $label }}">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="{{ $field }}">{{ old('description', $category->description) }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <label for="sort_order" class="{{ $label }}">Urutan</label>
            <input id="sort_order" name="sort_order" type="number" min="0" step="1" class="{{ $field }}" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
            @error('sort_order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-end pb-3">
            <label class="inline-flex items-center gap-2 text-sm font-semibold text-forest-800">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true)) class="rounded border-forest-900/20 text-forest-600 focus:ring-forest-600">
                Kategori aktif
            </label>
        </div>
    </div>
</div>
