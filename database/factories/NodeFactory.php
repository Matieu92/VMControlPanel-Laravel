<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Node>
 */
class NodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Node-' . fake()->word() . '-' . fake()->numberBetween(1, 9),
            'location_id' => Location::factory(),
            'ip_address' => fake()->ipv4(),
            'total_ram_mb' => fake()->randomElement([65536, 131072, 262144]),
            'total_cpu_cores' => fake()->randomElement([32, 64, 128]),
            'is_active' => true,
        ];
    }
}
