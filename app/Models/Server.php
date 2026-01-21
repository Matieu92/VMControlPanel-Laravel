<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AuditLog;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostname', 
        'user_id', 
        'subscription_id', 
        'node_id', 
        'operating_system_id', 
        'ip_address', 
        'status'
    ];

    public function operatingSystem()
    {
        return $this->belongsTo(OperatingSystem::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function node()
    {
        return $this->belongsTo(Node::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function getPlanAttribute()
    {
        return $this->subscription->plan ?? null;
    }

    public static function checkProvisioning()
    {
        $servers = self::where('status', 'provisioning')
            ->where('updated_at', '<=', now()->subMinutes(2))
            ->get();

        foreach ($servers as $server) {
            $server->update(['status' => 'stopped']);

            AuditLog::create([
                'user_id' => null,
                'action' => 'SYSTEM_PROVISIONING_COMPLETE',
                'details' => "Automatyczne zakończenie instalacji dla serwera {$server->hostname}.",
                'ip_address' => '127.0.0.1'
            ]);
        }
    }
}