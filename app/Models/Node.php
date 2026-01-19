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
}
