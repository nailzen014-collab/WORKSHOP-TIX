@php
    $field = 'mt-1 block w-full rounded-xl border-forest-900/20 bg-cream-50 text-forest-900 shadow-sm focus:border-forest-600 focus:ring-forest-600';
    $label = 'text-xs font-semibold uppercase tracking-wide text-forest-600';
@endphp

<div class="space-y-5">
    <div>
        <label for="title" class="{{ $label }}">Judul Workshop</label>
        <input id="title" name="title" type="text" class="{{ $field }}" value="{{ old('title', $workshop->title) }}" required>
        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="{{ $label }}">Deskripsi</label>
        <textarea id="description" name="description" rows="5" class="{{ $field }}" required>{{ old('description', $workshop->description) }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="image" class="{{ $label }}">{{ $workshop->image_url ? 'Ganti Gambar Workshop (opsional)' : 'Gambar Workshop (opsional)' }}</label>
        @if ($workshop->image_url)
            <img src="{{ $workshop->image_url }}" alt="Gambar {{ $workshop->title }}" class="mt-2 aspect-[16/9] w-full rounded-2xl object-cover">
        @endif
        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="{{ $field }}">
        <p class="mt-1 text-xs text-forest-500">Pilih gambar baru untuk mengganti gambar saat ini. Format JPG, PNG, atau WebP, maksimal 5 MB. Gambar disimpan di penyimpanan lokal.</p>
        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="category_id" class="{{ $label }}">Kategori</label>
        <select id="category_id" name="category_id" class="{{ $field }}">
            <option value="">Tanpa kategori</option>
            @foreach ($categories ?? [] as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $workshop->category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <label for="price" class="{{ $label }}">Harga (Rp)</label>
            <input id="price" name="price" type="number" min="0" step="1" class="{{ $field }}" value="{{ old('price', $workshop->price) }}" required>
            @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="location" class="{{ $label }}">Lokasi</label>
            <input id="location" name="location" type="text" class="{{ $field }}" value="{{ old('location', $workshop->location) }}" required>
            @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="starts_at" class="{{ $label }}">Mulai</label>
            <input id="starts_at" name="starts_at" type="datetime-local" class="{{ $field }}" value="{{ old('starts_at', optional($workshop->starts_at)->format('Y-m-d\TH:i')) }}" required>
            @error('starts_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="ends_at" class="{{ $label }}">Selesai (opsional)</label>
            <input id="ends_at" name="ends_at" type="datetime-local" class="{{ $field }}" value="{{ old('ends_at', optional($workshop->ends_at)->format('Y-m-d\TH:i')) }}">
            @error('ends_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="status" class="{{ $label }}">Status</label>
        <select id="status" name="status" class="{{ $field }}">
            <option value="draft" @selected(old('status', $workshop->status) === 'draft')>Draft</option>
            <option value="published" @selected(old('status', $workshop->status) === 'published')>Published</option>
        </select>
        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
