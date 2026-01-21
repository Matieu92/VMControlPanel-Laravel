@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dziennik Zdarzeń</h1>
    <p class="page-subtitle">Pełna historia operacji wykonanych przez użytkowników oraz automat systemowy.</p>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Data i Godzina</th>
                <th>Inicjator</th>
                <th>Typ Akcji</th>
                <th>Szczegóły Operacji</th>
                <th>Adres IP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td style="white-space: nowrap; font-family: monospace; font-size: 0.9rem;">
                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                </td>
                <td>
                    @if($log->user)
                        <div style="font-weight: 600;">{{ $log->user->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">ID: #{{ $log->user_id }}</div>
                    @else
                        <span style="color: var(--primary); font-weight: 800; letter-spacing: 1px;">[SYSTEM]</span>
                    @endif
                </td>
                <td>
                    <span class="action-tag">{{ $log->action }}</span>
                </td>
                <td style="font-size: 0.9rem; line-height: 1.4; max-width: 400px;">
                    {{ $log->details }}
                </td>
                <td style="font-family: monospace; font-size: 0.85rem; color: var(--text-muted);">
                    {{ $log->ip_address }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $logs->links() }}
    </div>
</div>

<style>
    .action-tag {
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        padding: 4px 8px;
        border-radius: 4px;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.75rem;
        font-weight: bold;
        text-transform: uppercase;
    }

    tr:has(span[style*="[SYSTEM]"]) {
        background-color: rgba(59, 130, 246, 0.02);
    }

    .pagination {
        display: flex;
        gap: 5px;
        list-style: none;
        padding: 0;
    }
</style>
@endsection