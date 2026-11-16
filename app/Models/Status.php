<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['entity', 'code', 'name', 'color', 'sort_order', 'is_active'])]
class Status extends Model
{
    use HasFactory;

    public const ENTITY_WORKSHOP = 'workshop';

    public const ENTITY_REGISTRATION = 'registration';

    public const ENTITY_PAYMENT = 'payment';

    public const ENTITY_TICKET = 'ticket';

    public const ENTITY_USER_ROLE = 'user_role';

    /**
     * @return array<string, string>
     */
    public static function entities(): array
    {
        return [
            self::ENTITY_WORKSHOP => 'Workshop',
            self::ENTITY_REGISTRATION => 'Pendaftaran',
            self::ENTITY_PAYMENT => 'Pembayaran',
            self::ENTITY_TICKET => 'Tiket',
            self::ENTITY_USER_ROLE => 'Role Pengguna',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    #[Scope]
    protected function forEntity(Builder $query, string $entity): Builder
    {
        return $query->where('entity', $entity);
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
