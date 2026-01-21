@extends('layouts.app')

@push('styles')
<style>
    .admin-header {
        margin-bottom: 2rem;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--bg-card);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .data-table th {
        text-align: left;
        padding: 1rem;
        background: var(--bg-body);
        color: var(--text-muted);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05rem;
        border-bottom: 1px solid var(--border-color);
    }

    .data-table td {
        padding: 1.25rem 1rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .status-pill {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .status-open { background: rgba(22, 163, 74, 0.1); color: #16a34a; border: 1px solid #16a34a; }
    .status-answered { background: rgba(37, 99, 235, 0.1); color: #2563eb; border: 1px solid #2563eb; }
    .status-closed { background: rgba(107, 114, 128, 0.1); color: #6b7280; border: 1px solid #6b7280; }

    .user-box {
        display: flex;
        flex-direction: column;
    }
    .user-name { font-weight: 700; color: var(--text-main); font-size: 0.9rem; }
    .user-email { font-size: 0.8rem; color: var(--text-muted); }

    .subject-link {
        font-weight: 600;
        color: var(--primary);
        text-decoration: none;
        display: block;
        margin-bottom: 0.25rem;
    }
    .subject-link:hover { text-decoration: underline; }
    .category-tag { font-size: 0.8rem; color: var(--text-muted); }

    .priority-indicator {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .dot { width: 10px; height: 10px; border-radius: 50%; }
    .dot-high { background: #dc2626; box-shadow: 0 0 5px rgba(220, 38, 38, 0.5); }
    .dot-medium { background: #d97706; }
    .dot-low { background: #16a34a; }

    .actions-flex {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .btn-stop  { 
        background-color: #dc2626; 
        color: #fff;
    }

    body.high-contrast .btn-stop {
        font-weight: 1000;
    }
</style>
@endpush

@section('content')
<div class="admin-header">
    <h1 class="page-title">Zarządzanie Zgłoszeniami</h1>
    <p class="page-subtitle">Przeglądaj i odpowiadaj na zapytania wszystkich użytkowników systemu.</p>
</div>

    <table class="data-table" aria-label="Lista wszystkich zgłoszeń technicznych">
        <thead>
            <tr>
                <th scope="col">Status</th>
                <th scope="col">Klient</th>
                <th scope="col">Temat / Kategoria</th>
                <th scope="col">Priorytet</th>
                <th scope="col" style="text-align: right;">Akcje</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            <tr>
                <td>
                    <span class="status-pill status-{{ $ticket->status }}">
                        {{ $ticket->status }}
                    </span>
                </td>
                <td>
                    <div class="user-box">
                        <span class="user-name">{{ $ticket->user->name }}</span>
                        <span class="user-email">{{ $ticket->user->email }}</span>
                    </div>
                </td>
                <td>
                    <span class="subject-link">{{ $ticket->subject }}</span>
                    <span class="category-tag">
                        Kategoria: <strong>{{ ucfirst($ticket->category) }}</strong>
                        @if($ticket->server)
                            | Serwer: <strong>{{ $ticket->server->hostname }}</strong>
                        @endif
                    </span>
                </td>
                <td>
                    <div class="priority-indicator">
                        <span class="dot dot-{{ $ticket->priority }}" aria-hidden="true"></span>
                        {{ ucfirst($ticket->priority) }}
                    </div>
                </td>
                <td style="text-align: right;">
                    <div class="actions-flex">
                        <a href="{{ route('support.show', $ticket) }}" class="btn btn-primary">Odpowiedz</a>
                        
                        @if($ticket->status !== 'closed')
                            <form action="{{ route('admin.support.close', $ticket) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-stop">Zamknij</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                    Brak zgłoszeń wymagających uwagi.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

@if($tickets->hasPages())
    <div style="margin-top: 1.5rem;">
        {{ $tickets->links() }}
    </div>
@endif
@endsection