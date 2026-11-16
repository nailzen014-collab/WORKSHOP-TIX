<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Status>
 */
class StatusFactory extends Factory
{
    protected $model = Status::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'entity' => fake()->randomElement(array_keys(Status::entities())),
            'code' => Str::slug($name, '_').'_'.fake()->unique()->numberBetween(1, 99999),
            'name' => Str::title($name),
            'color' => fake()->randomElement(['lime', 'amber', 'red', 'forest', 'neutral']),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
