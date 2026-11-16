<?php

namespace Database\Factories;

use App\Models\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PaymentType>
 */
class PaymentTypeFactory extends Factory
{
    protected $model = PaymentType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'code' => Str::slug($name, '_').'_'.fake()->unique()->numberBetween(1, 99999),
            'name' => Str::title($name),
            'group' => fake()->randomElement(['bank_transfer', 'ewallet', 'qris', 'card', 'retail', 'other']),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
