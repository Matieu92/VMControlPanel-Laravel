@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Wszystkie Serwery</h1>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Klient</th>
                <th>Hostname</th>
                <th>Węzeł (Node)</th>
                <th>Plan</th>
                <th>System</th>
                <th>Status</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($servers as $server)
            <tr>
                <td>#{{ $server->id }}</td>
                <td>
                    <div style="font-weight: bold;">{{ $server->user->name }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $server->user->email }}</div>
                </td>
                <td>{{ $server->hostname }}</td>
                <td>
                    @if($server->node)
                        <span style="font-family: monospace; background: var(--bg-body); padding: 2px 6px; border-radius: 4px; border: 1px solid var(--border-color);">
                            {{ $server->node->name }}
                        </span>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                            {{ $server->node->location->city ?? 'Lokalizacja nieznana' }}
                        </div>
                    @else
                        <span style="color: var(--danger); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid var(--danger); padding: 2px 6px; border-radius: 4px; background: rgba(229, 62, 62, 0.1);">
                            Nie przypisano
                        </span>
                        <div style="font-size: 0.75rem; color: var(--danger); margin-top: 2px;">
                            Wymagana interwencja administratora
                        </div>
                    @endif
                </td>
                <td>{{ $server->plan->name ?? 'Custom' }}</td>
                <td>{{ $server->operatingsystem->name." ".$server->operatingsystem->version ?? 'Custom' }}</td>
                <td>
                    @if($server->status === 'running')
                        <span class="status-badge status-running">Działa</span>
                    @elseif($server->status === 'provisioning')
                        <span class="status-badge status-provisioning">Instalacja</span>
                    @else
                        <span class="status-badge status-stopped">{{ ucfirst($server->status) }}</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.servers.migrate', $server) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">
                        Migruj
                    </a>

                    <a href="{{ route('admin.servers.edit_plan', $server) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">
                        Zmień Plan
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection