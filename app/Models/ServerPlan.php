<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerPlan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'ram_mb', 'cpu_cores'];

    public function operatingSystems()
    {
        return $this->belongsToMany(OperatingSystem::class, 'operating_system_server_plan');
    }
}