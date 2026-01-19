<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerPlan extends Model
{
    public function operatingSystems() {
        return $this->belongsToMany(OperatingSystem::class, 'operating_system_server_plan');
    }
}
