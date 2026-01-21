<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OperatingSystem>
 */
class OperatingSystemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $systems = ['Ubuntu', 'Debian', 'CentOS', 'AlmaLinux'];
        return [
            'name' => fake()->randomElement($systems),
            'version' => fake()->randomFloat(1, 10, 24) . ' LTS',
        ];
    }
}
