<?php

namespace Database\Seeders;

use App\Models\WorkshopCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorkshopCategorySeeder extends Seeder
{
    /**
     * Seed workshop categories.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Web Development', 'description' => 'Pengembangan aplikasi dan situs web.'],
            ['name' => 'UI/UX Design', 'description' => 'Desain antarmuka dan pengalaman pengguna.'],
            ['name' => 'Data Science', 'description' => 'Analisis data, machine learning, dan AI.'],
            ['name' => 'Mobile Apps', 'description' => 'Pengembangan aplikasi Android dan iOS.'],
            ['name' => 'Cyber Security', 'description' => 'Keamanan sistem dan jaringan.'],
            ['name' => 'Digital Marketing', 'description' => 'Pemasaran digital dan media sosial.'],
            ['name' => 'Umum', 'description' => 'Workshop umum di luar kategori khusus.'],
        ];

        foreach ($categories as $index => $category) {
            WorkshopCategory::query()->updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                    'sort_order' => $index,
                ],
            );
        }
    }
}
