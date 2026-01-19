<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'server_plan_id', 
        'starts_at', 
        'ends_at', 
        'status'
    ];

    public function plan()
    {
        return $this->belongsTo(ServerPlan::class, 'server_plan_id');
    }

    public function servers()
    {
        return $this->hasMany(Server::class);
    }
}