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
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\Transaction;
use App\Models\AuditLog;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $commonPassword = Hash::make('password');

        $admin = User::create([
            'name' => 'Administrator Systemu',
            'email' => 'admin@vm.pl',
            'role' => 'admin',
            'balance' => 0,
            'password' => $commonPassword,
        ]);

        $support = User::create([
            'name' => 'Technik Wsparcia',
            'email' => 'support@vm.pl',
            'role' => 'support',
            'balance' => 0,
            'password' => $commonPassword,
        ]);

        $clients = [];
        $clientNames = ['Jan Kowalski', 'Anna Nowak', 'Marek Wiśniewski', 'Zofia Zielińska'];
        foreach ($clientNames as $index => $name) {
            $clients[] = User::create([
                'name' => $name,
                'email' => strtolower(explode(' ', $name)[0]) . '@test.pl',
                'role' => 'client',
                'balance' => 100 * ($index + 1),
                'password' => $commonPassword,
            ]);
        }

        $locs = [
            Location::create(['city' => 'Warszawa', 'country_code' => 'PL']),
            Location::create(['city' => 'Krakow', 'country_code' => 'PL']),
            Location::create(['city' => 'Frankfurt', 'country_code' => 'DE']),
            Location::create(['city' => 'Londyn', 'country_code' => 'GB']),
            Location::create(['city' => 'Amsterdam', 'country_code' => 'NL']),
            Location::create(['city' => 'Nowy Jork', 'country_code' => 'US']),
        ];

        $nodes = [];
        foreach ($locs as $i => $l) {
            $nodes[] = Node::create([
                'name' => 'Node-' . strtoupper($l->city) . '-0' . ($i + 1),
                'location_id' => $l->id,
                'ip_address' => '185.255.' . ($i + 10) . '.1',
                'total_ram_mb' => 262144,
                'total_cpu_cores' => 128,
                'is_active' => true
            ]);
        }

        $systems = [
            ['Ubuntu', '24.04 LTS (Noble Numbat)'],
            ['Ubuntu', '22.04 LTS (Jammy Jellyfish)'],
            ['Ubuntu', '20.04 LTS (Focal Fossa)'],
            ['Debian', '12 (Bookworm)'],
            ['Debian', '11 (Bullseye)'],
            ['CentOS', 'Stream 9'],
            ['CentOS', 'Stream 8'],
            ['AlmaLinux', '9.4'],
            ['AlmaLinux', '8.9'],
            ['Rocky Linux', '9.3'],
            ['Windows Server', '2022 Standard'],
            ['Windows Server', '2019 Standard'],
        ];

        $osModels = [];
        foreach ($systems as $s) {
            $osModels[] = OperatingSystem::create(['name' => $s[0], 'version' => $s[1]]);
        }

        $planSmall = ServerPlan::create(['name' => 'Micro VPS', 'price' => 19.00, 'ram_mb' => 2048, 'cpu_cores' => 1]);
        $planMedium = ServerPlan::create(['name' => 'Standard VPS', 'price' => 49.00, 'ram_mb' => 4096, 'cpu_cores' => 2]);
        $planLarge = ServerPlan::create(['name' => 'Pro VPS', 'price' => 99.00, 'ram_mb' => 8192, 'cpu_cores' => 4]);

        $planSmall->operatingSystems()->attach([$osModels[0]->id, $osModels[1]->id, $osModels[3]->id]);
        $planMedium->operatingSystems()->attach([$osModels[0]->id, $osModels[1]->id, $osModels[2]->id, $osModels[3]->id, $osModels[4]->id, $osModels[5]->id]);
        $planLarge->operatingSystems()->attach(collect($osModels)->pluck('id')->toArray());

        for ($i = 0; $i < 6; $i++) {
            Subscription::create([
                'user_id' => $clients[$i % 4]->id,
                'server_plan_id' => $i < 2 ? $planSmall->id : ($i < 4 ? $planMedium->id : $planLarge->id),
                'starts_at' => now()->subDays($i),
                'ends_at' => now()->addMonth(),
                'status' => 'active'
            ]);
        }

        $allSubs = Subscription::all();
        foreach ($allSubs as $i => $s) {
            Server::create([
                'hostname' => 'vm-' . (100 + $i) . '.vm-control.pl',
                'user_id' => $s->user_id,
                'subscription_id' => $s->id,
                'node_id' => $nodes[$i % 6]->id,
                'operating_system_id' => $osModels[$i % count($osModels)]->id,
                'ip_address' => '185.255.10.' . ($i + 50),
                'status' => 'running'
            ]);
        }

        $categories = ['technical', 'billing', 'security', 'migration'];
        for ($i = 0; $i < 6; $i++) {
            SupportTicket::create([
                'user_id' => $clients[$i % 4]->id,
                'category' => $categories[$i % 4],
                'subject' => 'Zgłoszenie serwisowe #' . (1000 + $i),
                'priority' => $i % 3 == 0 ? 'high' : ($i % 2 == 0 ? 'medium' : 'low'),
                'status' => $i < 3 ? 'open' : 'closed',
            ]);
        }

        $tickets = SupportTicket::all();
        foreach ($tickets as $t) {
            TicketMessage::create([
                'support_ticket_id' => $t->id,
                'user_id' => $t->user_id,
                'message' => 'Dzień dobry, proszę o pomoc z konfiguracją systemu.'
            ]);
        }

        foreach ($clients as $c) {
            Transaction::create(['user_id' => $c->id, 'amount' => 100.00, 'type' => 'deposit', 'description' => 'Zasilenie konta']);
            Transaction::create(['user_id' => $c->id, 'amount' => -19.00, 'type' => 'payment', 'description' => 'Opłata za serwer']);
        }

        for ($i = 0; $i < 6; $i++) {
            AuditLog::create([
                'user_id' => $clients[$i % 4]->id,
                'action' => 'Server Boot',
                'details' => 'Użytkownik uruchomił instancję VPS',
                'ip_address' => '127.0.0.1'
            ]);
        }
    }
}