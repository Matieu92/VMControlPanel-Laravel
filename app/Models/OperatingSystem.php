<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatingSystem extends Model
{
    public function serverPlans() {
        return $this->belongsToMany(ServerPlan::class, 'operating_system_server_plan');
    }
}
