<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Node;
use App\Models\OperatingSystem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hostname' => 'vm-' . fake()->unique()->numberBetween(1000, 9999),
            'user_id' => User::factory(),
            'subscription_id' => Subscription::factory(),
            'node_id' => Node::factory(),
            'operating_system_id' => OperatingSystem::factory(),
            'ip_address' => fake()->ipv4(),
            'root_password' => Str::random(12),
            'status' => 'running',
        ];
    }
}