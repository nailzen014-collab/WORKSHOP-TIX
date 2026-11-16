<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-full bg-forest-900 px-5 py-2.5 text-sm font-semibold text-cream-50 transition-colors hover:bg-forest-700 focus:outline-none focus:ring-2 focus:ring-forest-600 focus:ring-offset-2 focus:ring-offset-cream-100 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
