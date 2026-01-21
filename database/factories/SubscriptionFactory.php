<?php

namespace Database\Factories;
use App\Models\User;
use App\Models\ServerPlan;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
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
            'server_plan_id' => ServerPlan::factory(),
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'status' => 'active',
        ];
    }
}
