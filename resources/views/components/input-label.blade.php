@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold uppercase tracking-wide text-forest-600']) }}>
    {{ $value ?? $slot }}
</label>
