@props(['entity', 'code' => null, 'fallback' => null])

@php($presenter = app(\App\Services\StatusPresenter::class))

<span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $presenter->badgeClasses($entity, $code) }}">
    {{ $presenter->label($entity, $code, $fallback) }}
</span>
