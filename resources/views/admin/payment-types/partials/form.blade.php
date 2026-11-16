@php
    $field = 'mt-1 block w-full rounded-xl border-forest-900/20 bg-cream-50 text-forest-900 shadow-sm focus:border-forest-600 focus:ring-forest-600';
    $label = 'text-xs font-semibold uppercase tracking-wide text-forest-600';
@endphp

<div class="space-y-5">
    <div>
        <label for="name" class="{{ $label }}">Nama Jenis Pembayaran</label>
        <input id="name" name="name" type="text" class="{{ $field }}" value="{{ old('name', $paymentType->name) }}" required>
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="code" class="{{ $label }}">Kode Midtrans</label>
        <input id="code" name="code" type="text" class="{{ $field }}" value="{{ old('code', $paymentType->code) }}" placeholder="contoh: gopay, qris, bank_transfer" required>
        <p class="mt-1 text-xs text-forest-500">Gunakan kode yang sama dengan nilai <code>payment_type</code> dari Midtrans agar pemetaan otomatis berjalan.</p>
        @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="group" class="{{ $label }}">Grup</label>
        <select id="group" name="group" class="{{ $field }}">
            <option value="">Tanpa grup</option>
            @foreach ($groups as $value => $groupLabel)
                <option value="{{ $value }}" @selected(old('group', $paymentType->group) === $value)>{{ $groupLabel }}</option>
            @endforeach
        </select>
        @error('group') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <label for="sort_order" class="{{ $label }}">Urutan</label>
            <input id="sort_order" name="sort_order" type="number" min="0" step="1" class="{{ $field }}" value="{{ old('sort_order', $paymentType->sort_order ?? 0) }}">
            @error('sort_order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-end pb-3">
            <label class="inline-flex items-center gap-2 text-sm font-semibold text-forest-800">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $paymentType->is_active ?? true)) class="rounded border-forest-900/20 text-forest-600 focus:ring-forest-600">
                Jenis pembayaran aktif
            </label>
        </div>
    </div>
</div>
