<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'registration_id' => Registration::factory(),
            'order_id' => 'WTX-'.Str::uuid(),
            'amount' => 100000,
            'payment_status' => Payment::STATUS_PENDING,
            'paid_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => Payment::STATUS_SETTLEMENT,
            'paid_at' => now(),
        ]);
    }
}
