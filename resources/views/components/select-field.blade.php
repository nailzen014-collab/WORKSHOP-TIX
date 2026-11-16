@props(['label', 'name', 'value' => null, 'options' => [], 'placeholder' => null])

@php
    $id = $attributes->get('id', $name);
    $wrapperClass = $attributes->get('class', 'w-full sm:w-48');
@endphp

<div class="{{ $wrapperClass }}">
    <label for="{{ $id }}" class="text-xs font-semibold uppercase tracking-wide text-forest-600">{{ $label }}</label>
    <select
        id="{{ $id }}"
        name="{{ $name }}"
        {{ $attributes->except(['class', 'id'])->merge(['class' => 'mt-1 block w-full rounded-xl border-forest-900/20 bg-cream-50 text-sm text-forest-900 shadow-sm focus:border-forest-600 focus:ring-forest-600']) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) old($name, $value) === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
</div>
