<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Location;
use App\Models\Node;
use App\Models\OperatingSystem;
use App\Models\ServerPlan;
use App\Models\Subscription;
use App\Models\Server;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $commonPassword = Hash::make('password');

        User::factory()->create([
            'name' => 'Administrator Systemu',
            'email' => 'admin@vm.pl',
            'role' => 'admin',
            'password' => $commonPassword,
        ]);

        $client = User::factory()->create([
            'name' => 'Jan Kowalski',
            'email' => 'klient@test.pl',
            'role' => 'client',
            'password' => $commonPassword,
        ]);

        $loc = Location::create(['city' => 'Warszawa', 'country_code' => 'PL']);

        $node = Node::create([
            'name' => 'Node-WAW-01',
            'location_id' => $loc->id,
            'ip_address' => '192.168.100.1',
            'total_ram_mb' => 32768,
            'total_cpu_cores' => 16,
            'is_active' => true
        ]);

        $ubuntu = OperatingSystem::create(['name' => 'Ubuntu', 'version' => '22.04 LTS']);
        $debian = OperatingSystem::create(['name' => 'Debian', 'version' => '12 Bookworm']);
        $centos = OperatingSystem::create(['name' => 'CentOS', 'version' => 'Stream 9']);

        $planSmall = ServerPlan::create(['name' => 'Micro VPS', 'price' => 19.00, 'ram_mb' => 2048, 'cpu_cores' => 1]);
        $planMedium = ServerPlan::create(['name' => 'Standard VPS', 'price' => 49.00, 'ram_mb' => 4096, 'cpu_cores' => 2]);
        $planLarge = ServerPlan::create(['name' => 'Pro VPS', 'price' => 99.00, 'ram_mb' => 8192, 'cpu_cores' => 4]);

        $planSmall->operatingSystems()->attach($ubuntu->id);
        $planMedium->operatingSystems()->attach([$ubuntu->id, $debian->id]);
        $planLarge->operatingSystems()->attach([$ubuntu->id, $debian->id, $centos->id]);

        $sub = Subscription::create([
            'user_id' => $client->id,
            'server_plan_id' => $planSmall->id,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'status' => 'active'
        ]);

        Server::create([
            'hostname' => 'moj-serwer-www',
            'user_id' => $client->id,
            'subscription_id' => $sub->id,
            'node_id' => $node->id,
            'operating_system_id' => $ubuntu->id,
            'ip_address' => '10.10.20.50',
            'status' => 'running'
        ]);
    }
}