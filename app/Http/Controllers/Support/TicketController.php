<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function reply(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:5',
        ]);

        TicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        $newStatus = (auth()->user()->role === 'client') ? 'open' : 'answered';
        $ticket->update(['status' => $newStatus]);

        return back()->with('success', 'Wiadomość została wysłana.');
    }
}