<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OperatingSystem extends Model
{
    use HasFactory;

    public function serverPlans() {
        return $this->belongsToMany(ServerPlan::class, 'operating_system_server_plan');
    }

    protected $fillable = ['name', 'version'];

    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }
}
