<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\Server;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        $servers = Server::where('user_id', Auth::id())->get();
        return view('support.create', compact('servers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'subject' => 'required|string|min:5|max:255',
            'message' => 'required|string|min:10',
            'server_id' => 'nullable|exists:servers,id'
        ]);

        $priorityMap = [
            'emergency' => 'high',
            'security'  => 'high',
            'technical' => 'high',
            'upgrade'   => 'medium',
            'billing'   => 'medium',
            'migration' => 'medium',
            'feature'   => 'low',
            'other'     => 'low'
        ];

        $priority = $priorityMap[$request->category] ?? 'medium';

        return \DB::transaction(function () use ($validated, $request, $priority) {
            $ticket = \App\Models\SupportTicket::create([
                'user_id' => auth()->id(),
                'server_id' => $validated['server_id'],
                'category' => $validated['category'],
                'subject' => $validated['subject'],
                'priority' => $priority,
                'status' => 'open'
            ]);

            \App\Models\TicketMessage::create([
                'support_ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'message' => $validated['message']
            ]);

            return redirect()->route('support.show', $ticket)->with('success', 'Zgłoszenie zostało wysłane.');
        });
    }

    public function show(SupportTicket $ticket)
    {
        if (auth()->user()->role !== 'admin' && $ticket->user_id !== auth()->id()) {
            abort(403, 'Brak uprawnień do tego zgłoszenia.');
        }

        $ticket->load(['messages.user', 'server']);
        return view('support.show', compact('ticket'));
    }

    public function sendMessage(Request $request, SupportTicket $ticket)
    {
        if ($ticket->status === 'closed') {
            return back()->with('error', 'To zgłoszenie jest zamknięte.');
        }

        $validated = $request->validate([
            'message' => 'required|string|min:5',
        ]);

        DB::transaction(function () use ($validated, $ticket, $request) {
            TicketMessage::create([
                'support_ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'message' => $validated['message'],
            ]);

            $newStatus = (Auth::user()->role === 'client') ? 'open' : 'answered';
            $ticket->update(['status' => $newStatus]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'TICKET_REPLY',
                'details' => "Odpowiedź w zgłoszeniu #{$ticket->id}",
                'ip_address' => $request->ip()
            ]);
        });

        return back()->with('success', 'Wiadomość została wysłana.');
    }
}