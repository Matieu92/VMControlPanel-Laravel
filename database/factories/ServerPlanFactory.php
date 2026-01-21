<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServerPlan>
 */
class ServerPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word() . ' Plan',
            'price' => fake()->randomElement([19, 49, 99]),
            'ram_mb' => fake()->randomElement([2048, 4096, 8192]),
            'cpu_cores' => fake()->randomElement([1, 2, 4]),
        ];
    }
}
