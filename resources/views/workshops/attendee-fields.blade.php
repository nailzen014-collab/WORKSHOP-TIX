<div>
    <p class="text-xs leading-relaxed text-forest-600">Isi nama lengkap setiap peserta. Maksimal 5 tiket per workshop.</p>

    <div data-attendee-rows class="mt-3 space-y-4">
        @foreach (old('attendees', ['']) as $index => $attendee)
            <div data-attendee-row class="space-y-2">
                <label for="attendee-{{ $index }}" class="text-xs font-semibold uppercase tracking-wide text-forest-600">
                    Nama lengkap peserta {{ $index + 1 }}
                </label>
                <input
                    id="attendee-{{ $index }}"
                    name="attendees[]"
                    type="text"
                    value="{{ $attendee }}"
                    autocomplete="name"
                    maxlength="255"
                    required
                    class="block w-full rounded-xl border-forest-900/20 bg-cream-50 text-sm text-forest-900 shadow-sm focus:border-forest-600 focus:ring-forest-600"
                >
                @error('attendees.'.$index)
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endforeach
    </div>

    @error('attendees')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <button
        type="button"
        data-add-attendee
        hidden
        class="mt-3 text-sm font-semibold text-forest-600 underline hover:text-forest-900"
    >
        Tambah tiket
    </button>
</div>
