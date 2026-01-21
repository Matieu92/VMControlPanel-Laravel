@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Panel Administracyjny</h1>
    <p class="page-subtitle">Przegląd infrastruktury i aktywności systemu.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-label">Instancje (Razem)</span>
        <span class="stat-value">{{ $stats['total_servers'] }}</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Aktywne (Online)</span>
        <span class="stat-value" style="color: var(--success);">{{ $stats['active_servers'] }}</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">W instalacji</span>
        <span class="stat-value" style="color: var(--primary);">{{ $stats['provisioning'] }}</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Wykorzystanie RAM</span>
        <span class="stat-value">{{ number_format($stats['used_ram'] / 1024, 1) }} / {{ number_format($stats['total_ram'] / 1024, 1) }} GB</span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 30px;">
    <div class="card">
        <h3 class="h3">Ostatnio utworzone serwery</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Klient</th>
                    <th>Hostname</th>
                    <th>Plan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentServers as $srv)
                <tr>
                    <td>{{ $srv->user->name }}</td>
                    <td>{{ $srv->hostname }}</td>
                    <td>{{ $srv->subscription->plan->name ?? 'N/A' }}</td>
                    <td><span class="status-badge status-{{ $srv->status }}">{{ strtoupper($srv->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3 class="h3">Ostatnia aktywność</h3>
        <div class="log-feed">
            @foreach($latestLogs as $log)
            <div class="log-item">
                <div class="log-meta">
                    <span class="log-user">{{ $log->user->name ?? 'SYSTEM' }}</span>
                    <span class="log-time">{{ $log->created_at->diffForHumans() }}</span>
                </div>
                <div class="log-action">{{ $log->action }}</div>
            </div>
            @endforeach
        </div>
        <a href="{{ route('admin.logs.index') }}" class="btn" style="width: 100%; margin-top: 15px; text-align: center;">Zobacz wszystkie logi</a>
    </div>
</div>

<style>
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
    .stat-card { background: var(--bg-card); padding: 20px; border-radius: 8px; border: 1px solid var(--border-color); }
    .stat-label { display: block; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
    .stat-value { display: block; font-size: 1.8rem; font-weight: 800; margin-top: 5px; }

    .log-feed { display: flex; flex-direction: column; gap: 12px; }
    .log-item { padding-bottom: 12px; border-bottom: 1px solid var(--border-color); }
    .log-item:last-child { border: none; }
    .log-meta { display: flex; justify-content: space-between; font-size: 0.75rem; margin-bottom: 4px; }
    .log-user { font-weight: bold; color: var(--primary); }
    .log-time { color: var(--text-muted); }
    .log-action { font-size: 0.85rem; font-family: monospace; }
</style>
@endsection