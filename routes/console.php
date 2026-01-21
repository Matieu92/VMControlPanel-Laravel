<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Server;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $servers = Server::where('status', 'provisioning')
        ->where('updated_at', '<=', now()->subMinutes(2))
        ->get();

    foreach ($servers as $server) {
        $server->update(['status' => 'stopped']);
        
        AuditLog::create([
            'user_id' => null,
            'action' => 'SYSTEM_PROVISIONING_COMPLETE',
            'details' => "Automatyczne zakończenie instalacji dla serwera {$server->hostname} (ID: {$server->id}).",
            'ip_address' => '127.0.0.1'
        ]);

        Log::info("Automat: Serwer {$server->hostname} (ID: {$server->id}) gotowy.");
    }
})->everyMinute();
