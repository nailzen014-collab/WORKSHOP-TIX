@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-forest-900/20 bg-cream-50 text-forest-900 shadow-sm focus:border-forest-600 focus:ring-forest-600']) }}>
