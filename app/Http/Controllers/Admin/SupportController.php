<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::with('user')
            ->orderByRaw("CASE 
                WHEN priority = 'high' THEN 1 
                WHEN priority = 'medium' THEN 2 
                ELSE 3 END")
            ->latest()
            ->paginate(15);

        return view('admin.support.index', compact('tickets'));
    }

    public function close(SupportTicket $ticket)
    {
        $ticket->update(['status' => 'closed']);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'TICKET_CLOSE',
            'details' => "Administrator zamknął zgłoszenie #{$ticket->id}",
            'ip_address' => request()->ip()
        ]);

        return back()->with('success', 'Zgłoszenie zostało zamknięte.');
    }
}