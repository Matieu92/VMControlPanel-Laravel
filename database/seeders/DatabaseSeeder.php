<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void {
        
        $loc = \App\Models\Location::create(['city' => 'Warszawa', 'country_code' => 'PL']);

        $node = \App\Models\Node::create([
            'name' => 'Node-01',
            'location_id' => $loc->id,
            'ip_address' => '10.0.0.1',
            'total_ram_mb' => 32768,
            'total_cpu_cores' => 16
        ]);

        $ubuntu = \App\Models\OperatingSystem::create(['name' => 'Ubuntu', 'version' => '22.04']);
        $debian = \App\Models\OperatingSystem::create(['name' => 'Debian', 'version' => '12']);

        $planSmall = \App\Models\ServerPlan::create(['name' => 'Small', 'price' => 19.99, 'ram_mb' => 2048, 'cpu_cores' => 1]);
        $planBig = \App\Models\ServerPlan::create(['name' => 'Big', 'price' => 49.99, 'ram_mb' => 8192, 'cpu_cores' => 4]);

        $planSmall->operatingSystems()->attach($ubuntu->id);
        $planBig->operatingSystems()->attach([$ubuntu->id, $debian->id]);

        $user = \App\Models\User::factory()->create(['role' => 'client', 'email' => 'klient@test.pl']);
        
        $sub = \App\Models\Subscription::create([
            'user_id' => $user->id,
            'server_plan_id' => $planSmall->id,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'status' => 'active'
        ]);

        \App\Models\Server::create([
            'hostname' => 'vps-testowy',
            'user_id' => $user->id,
            'subscription_id' => $sub->id,
            'node_id' => $node->id,
            'operating_system_id' => $ubuntu->id,
            'status' => 'running'
        ]);

    }
}
