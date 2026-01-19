<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    public function node() {
        return $this->belongsTo(Node::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function plan() {
        return $this->belongsTo(ServerPlan::class, 'server_plan_id');
    }
}
