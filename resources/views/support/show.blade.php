@extends('layouts.app')

@push('styles')
<style>
    .ticket-info-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 2rem;
    }

    .ticket-id-tag {
        font-size: 0.8rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
        display: block;
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 800;
        background: var(--primary);
        color: #fff;
    }

    .high-contrast .status-badge {
        color: #000;
        font-weight: 800;
    }

    .chat-window {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .message-row {
        display: flex;
        width: 100%;
    }

    .msg-client {
        justify-content: flex-end;
    }

    .msg-support {
        justify-content: flex-start;
    }

    .bubble {
        max-width: 70%;
        padding: 1.25rem;
        border-radius: 12px;
        position: relative;
        line-height: 1.5;
        font-size: 0.95rem;
    }

    .msg-client .bubble {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        border-bottom-right-radius: 2px;
        box-shadow: 4px 4px 0px rgba(0,0,0,0.05);
    }

    .msg-support .bubble {
        background: var(--primary);
        color: #fff;
        border-bottom-left-radius: 2px;
    }

    .high-contrast .msg-support .bubble {
        background: var(--primary);
        color: #000000;
        border-bottom-left-radius: 2px;
    }

    .bubble-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        opacity: 0.8;
    }

    .reply-box {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 2rem;
    }

    .form-label-top {
        display: block;
        margin-bottom: 1rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .textarea-full {
        width: 100%;
        min-height: 150px;
        padding: 1rem;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-main);
        font-family: inherit;
        font-size: 1rem;
        resize: vertical;
        margin-bottom: 1.5rem;
    }

    .action-bar {
        display: flex;
        justify-content: flex-end;
    }
</style>
@endpush

@section('content')
<div class="ticket-info-bar">
    <div>
        <span class="ticket-id-tag">Zgłoszenie #{{ $ticket->id }}</span>
        <h1 class="page-title" style="margin: 0.5rem 0 0.2rem 0;">{{ $ticket->subject }}</h1>
        
        <div style="font-size: 0.9rem; color: var(--text-muted); display: flex; align-items: center; gap: 5px;">
            <span style="font-weight: 600;">Dotyczy:</span>
            @if($ticket->server)
                <span style="color: var(--primary); font-weight: 600;">
                    {{ $ticket->server->hostname }} ({{ $ticket->server->ip_address }})
                </span>
            @else
                <span>Inne / Płatności</span>
            @endif
        </div>
    </div>
    <div style="text-align: right;">
        <span class="status-badge">{{ strtoupper($ticket->status) }}</span>
    </div>
</div>

<div class="chat-window">
    @foreach($ticket->messages as $msg)
        {{-- Logika kierunku wiadomości --}}
        <div class="message-row {{ $msg->user->role === 'client' ? 'msg-client' : 'msg-support' }}">
            <div class="bubble">
                <span class="bubble-meta">
                    <span>{{ $msg->user->name }}</span>
                    <span>{{ $msg->created_at->format('H:i, d.m.Y') }}</span>
                </span>
                <div class="bubble-content">
                    {!! nl2br(e($msg->message)) !!}
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($ticket->status !== 'closed')
    <div class="reply-box">
        <form action="{{ route('support.message', $ticket) }}" method="POST">
            @csrf
            <label for="reply_message" class="form-label-top">Twoja odpowiedź</label>
            <textarea 
                id="reply_message" 
                name="message" 
                class="textarea-full" 
                placeholder="Wpisz treść wiadomości..." 
                required
            ></textarea>
            
            <div class="action-bar">
                <button type="submit" class="btn btn-primary">Wyślij wiadomość</button>
            </div>
        </form>
    </div>
@else
    <div class="card" style="text-align: center; border: 3px dashed var(--border-color); background: transparent;">
        <p style="color: var(--text-muted); font-size: 0.9rem;">To zgłoszenie zostało zakończone. Jeśli masz nowe pytania, otwórz nowe zgłoszenie.</p>
    </div>
@endif
@endsection