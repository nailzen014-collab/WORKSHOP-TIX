@props(['action' => null, 'reset' => null])

<form method="GET" action="{{ $action ?? url()->current() }}" {{ $attributes->merge(['class' => 'card-cream p-4 sm:p-5']) }}>
    <div class="flex flex-wrap items-end gap-3">
        {{ $slot }}

        <div class="ml-auto flex items-center gap-2">
            <button type="submit" class="pill-button">Terapkan</button>
            <a href="{{ $reset ?? url()->current() }}" class="pill-button-outline">Reset</a>
        </div>
    </div>
</form>
