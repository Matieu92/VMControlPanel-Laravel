<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
    'user_id', 'server_id', 'subject', 'priority', 'status'
    ];
}
