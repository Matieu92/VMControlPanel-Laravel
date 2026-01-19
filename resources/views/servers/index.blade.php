@extends('layouts.app')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Moje Serwery</h1>
            <p class="page-subtitle">Zarządzaj swoimi instancjami wirtualnymi</p>
        </div>
        <a href="{{ route('servers.create') }}" class="btn btn-primary">
            Utwórz Serwer
        </a>
    </div>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th scope="col">Nazwa Hosta</th>
                <th scope="col">Adres IP</th>
                <th scope="col">Plan / System</th>
                <th scope="col">Status</th>
                <th scope="col">Akcje</th>
            </tr>
        </thead>
        <tbody>
            @forelse($servers as $server)
            <tr>
                <td><strong>{{ $server->hostname }}</strong></td>
                <td>{{ $server->ip_address ?? 'Oczekiwanie...' }}</td>
                <td>
                    <div>{{ $server->plan->name }}</div>
                    <small style="color: var(--text-muted);">{{ $server->operatingSystem->name }}</small>
                </td>
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
                    <a href="#" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">Zarządzaj</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    Nie posiadasz jeszcze żadnych aktywnych serwerów.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection