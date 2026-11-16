<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Seed status metadata for all main entities.
     */
    public function run(): void
    {
        $statuses = [
            'workshop' => [
                ['code' => 'draft', 'name' => 'Draft', 'color' => 'neutral'],
                ['code' => 'published', 'name' => 'Terbit', 'color' => 'lime'],
            ],
            'registration' => [
                ['code' => 'pending', 'name' => 'Menunggu', 'color' => 'amber'],
                ['code' => 'paid', 'name' => 'Lunas', 'color' => 'lime'],
            ],
            'payment' => [
                ['code' => 'pending', 'name' => 'Menunggu', 'color' => 'amber'],
                ['code' => 'settlement', 'name' => 'Lunas', 'color' => 'lime'],
                ['code' => 'capture', 'name' => 'Ditangkap', 'color' => 'lime'],
                ['code' => 'deny', 'name' => 'Ditolak', 'color' => 'red'],
                ['code' => 'expire', 'name' => 'Kedaluwarsa', 'color' => 'red'],
                ['code' => 'cancel', 'name' => 'Dibatalkan', 'color' => 'red'],
            ],
            'ticket' => [
                ['code' => 'active', 'name' => 'Aktif', 'color' => 'lime'],
                ['code' => 'used', 'name' => 'Terpakai', 'color' => 'neutral'],
                ['code' => 'cancelled', 'name' => 'Dibatalkan', 'color' => 'red'],
            ],
            'user_role' => [
                ['code' => 'admin', 'name' => 'Admin', 'color' => 'forest'],
                ['code' => 'peserta', 'name' => 'Peserta', 'color' => 'neutral'],
            ],
        ];

        foreach ($statuses as $entity => $items) {
            foreach ($items as $index => $item) {
                Status::query()->updateOrCreate(
                    ['entity' => $entity, 'code' => $item['code']],
                    [
                        'name' => $item['name'],
                        'color' => $item['color'],
                        'sort_order' => $index,
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
