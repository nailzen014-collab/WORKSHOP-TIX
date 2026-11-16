<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            WorkshopCategorySeeder::class,
            PaymentTypeSeeder::class,
            StatusSeeder::class,
        ]);

        User::query()->updateOrCreate(
            ['email' => 'admin@workshoptix.test'],
            [
                'name' => 'Admin Workshoptim',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'peserta@workshoptix.test'],
            [
                'name' => 'Peserta Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PESERTA,
            ],
        );

        $categoryIds = WorkshopCategory::query()->pluck('id');

        if (Workshop::query()->doesntExist()) {
            Workshop::factory()
                ->count(6)
                ->state(fn () => ['category_id' => $categoryIds->random()])
                ->create();
        } else {
            Workshop::query()
                ->whereNull('category_id')
                ->get()
                ->each(fn (Workshop $workshop) => $workshop->update(['category_id' => $categoryIds->random()]));
        }
    }
}
