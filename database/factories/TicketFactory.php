<?php

namespace Database\Factories;

use App\Models\Registration;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
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
            'ticket_code' => 'TIX-'.strtoupper(Str::random(10)),
            'status' => 'active',
            'issued_at' => now(),
        ];
    }
}
