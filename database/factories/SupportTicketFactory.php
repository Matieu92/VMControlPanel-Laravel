<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'server_id' => null,
            'category' => fake()->randomElement(['technical', 'billing', 'security']),
            'subject' => 'Zgloszenie techniczne #' . fake()->numberBetween(100, 999),
            'priority' => 'medium',
            'status' => 'open',
        ];
    }
}
