<?php

namespace App\Services;

use App\Models\Status;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StatusPresenter
{
    /**
     * @var array<string, Collection<string, Status>>
     */
    private array $cache = [];

    public function label(string $entity, ?string $code, ?string $fallback = null): string
    {
        if (! $code) {
            return $fallback ?? '-';
        }

        return $this->find($entity, $code)?->name
            ?? $fallback
            ?? Str::title(str_replace('_', ' ', $code));
    }

    public function badgeClasses(string $entity, ?string $code): string
    {
        $color = $this->find($entity, (string) $code)?->color;

        return match ($color) {
            'lime' => 'bg-lime-500 text-forest-900',
            'amber' => 'bg-amber-100 text-amber-800',
            'red' => 'bg-red-100 text-red-800',
            'forest' => 'bg-forest-900 text-cream-50',
            'neutral' => 'bg-cream-200 text-forest-600',
            default => $this->defaultBadgeClasses($entity, $code),
        };
    }

    private function find(string $entity, string $code): ?Status
    {
        $this->cache[$entity] ??= Status::query()
            ->where('entity', $entity)
            ->get()
            ->keyBy('code');

        return $this->cache[$entity]->get($code);
    }

    private function defaultBadgeClasses(string $entity, ?string $code): string
    {
        if (in_array($entity, [Status::ENTITY_PAYMENT, Status::ENTITY_REGISTRATION], true)) {
            $isPaid = in_array($code, ['settlement', 'capture', 'paid'], true);

            return $isPaid ? 'bg-lime-500 text-forest-900' : 'bg-amber-100 text-amber-800';
        }

        $isPositive = in_array($code, ['published', 'active'], true);

        return $isPositive
            ? 'bg-lime-500 text-forest-900'
            : 'bg-cream-200 text-forest-600';
    }
}
