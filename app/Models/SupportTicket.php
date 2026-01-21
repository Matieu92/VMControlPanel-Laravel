<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupportTicket extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 
        'server_id', 
        'category', 
        'subject', 
        'priority', 
        'status'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function server() {
        return $this->belongsTo(Server::class);
    }

    public function messages() {
        return $this->hasMany(TicketMessage::class);
    }
}