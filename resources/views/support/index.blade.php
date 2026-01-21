@extends('layouts.app')

@push('styles')
<style>
    .status-pill {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-open { background: rgba(22, 163, 74, 0.1); color: #16a34a; border: 1px solid #16a34a; }
    .status-answered { background: rgba(37, 99, 235, 0.1); color: #2563eb; border: 1px solid #2563eb; }
    .status-closed { background: rgba(107, 114, 128, 0.1); color: #6b7280; border: 1px solid #6b7280; }

    .priority-tag {
        display: flex;
        text-transform: uppercase;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
    }
    .priority-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .prio-high { background-color: #dc2626; box-shadow: 0 0 8px rgba(220, 38, 38, 0.4); }
    .prio-medium { background-color: #d97706; }
    .prio-low { background-color: #16a34a; }

    .ticket-row:hover {
        background-color: rgba(var(--primary-rgb), 0.02);
    }
    
    .ticket-subject {
        font-weight: 600;
        color: var(--text-main);
        text-decoration: none;
        display: block;
        margin-bottom: 0.2rem;
    }
    .ticket-subject:hover { color: var(--primary); }

    .ticket-meta {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .data-table th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; }
    .data-table td { padding: 1rem 0.75rem; vertical-align: middle; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Moje Zgłoszenia</h1>
            <p class="page-subtitle">Historia komunikacji z działem wsparcia technicznego.</p>
        </div>
        <a href="{{ route('support.create') }}" class="btn btn-primary">Nowe Zgłoszenie</a>
    </div>
</div>

<div class="card">
    <table class="data-table" aria-label="Lista zgłoszeń do pomocy technicznej">
        <thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Temat i Kategoria</th>
                <th>Priorytet</th>
                <th>Status</th>
                <th>Ostatnia aktualizacja</th>
                <th style="text-align: right;">Akcja</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
                <tr class="ticket-row">
                    <td style="font-family: monospace; color: var(--text-muted);">#{{ $ticket->id }}</td>
                    <td>
                        <a href="{{ route('support.show', $ticket) }}" class="ticket-subject">
                            {{ $ticket->subject }}
                        </a>
                        <span class="ticket-meta">
                            Kategoria: 
                            <strong>
                                @if($ticket->category)
                                    {{ ucfirst($ticket->category) }}
                                @elseif($ticket->server_id)
                                    Pomoc Techniczna
                                @else
                                    Zapytanie Ogólne
                                @endif
                            </strong>
                            @if($ticket->server)
                                | Serwer: <strong>{{ $ticket->server->hostname }}</strong>
                            @endif
                        </span>
                    </td>
                    <td>
                        <div class="priority-tag">
                            <span class="priority-dot prio-{{ $ticket->priority }}" aria-hidden="true"></span>
                            <span>{{ ucfirst($ticket->priority) }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="status-pill status-{{ $ticket->status }}">
                            {{ $ticket->status }}
                        </span>
                    </td>
                    <td style="font-size: 0.85rem; color: var(--text-muted);">
                        {{ $ticket->updated_at->diffForHumans() }}
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('support.show', $ticket) }}" class="btn btn-primary" 
                           style="padding: 0.4rem 0.8rem; font-size: 0.8rem; border: 1px solid var(--border-color);">
                            Pokaż rozmowę
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 4rem;">
                        <div style="color: var(--text-muted);">
                            <p style="font-size: 1.1rem; margin-bottom: 0.5rem;">Brak zgłoszeń do wyświetlenia.</p>
                            <p style="font-size: 0.9rem;">Jeśli masz problem, skorzystaj z przycisku powyżej.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($tickets->hasPages())
        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
@endsection