<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    public function servers() {
        return $this->hasMany(Server::class);
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }

    protected $fillable = [
    'name', 'location_id', 'ip_address', 'total_ram_mb', 'total_cpu_cores', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
