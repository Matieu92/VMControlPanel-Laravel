<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OperatingSystem extends Model
{
    use HasFactory;

    public function serverPlans() {
        return $this->belongsToMany(ServerPlan::class, 'operating_system_server_plan');
    }

    protected $fillable = ['name', 'version'];
}
