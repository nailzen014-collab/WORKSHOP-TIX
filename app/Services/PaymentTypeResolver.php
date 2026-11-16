<?php

namespace App\Services;

use App\Models\PaymentType;
use Illuminate\Support\Str;

class PaymentTypeResolver
{
    public function resolve(?string $code): ?int
    {
        $code = trim((string) $code);

        if ($code === '') {
            return null;
        }

        $code = substr($code, 0, 50);

        $type = PaymentType::query()->firstOrCreate(
            ['code' => $code],
            [
                'name' => Str::title(str_replace('_', ' ', $code)),
                'group' => null,
                'is_active' => true,
                'sort_order' => 0,
            ],
        );

        return $type->id;
    }
}
