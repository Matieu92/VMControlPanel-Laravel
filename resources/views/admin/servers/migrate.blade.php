@extends('layouts.app')

@section('content')
@push('styles')
<style>
    .migration-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .stat-box {
        background: var(--bg-body);
        padding: 15px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        display: block;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .warning-box {
        background-color: rgba(245, 158, 11, 0.1);
        border: 1px solid #f59e0b;
        color: #d97706;
        padding: 20px;
        border-radius: 6px;
        margin-top: 30px;
        margin-bottom: 30px;
        display: flex;
        gap: 15px;
        align-items: center;
    }

    body.high-contrast .stat-box {
        border: 2px solid #ffff00;
        background: #000;
    }
    body.high-contrast .warning-box {
        background: #000;
        border: 2px solid #ffff00;
        color: #ffff00;
    }
</style>
@endpush

<div class="page-header">
    <h1 class="page-title">Migracja Serwera</h1>
    <p class="page-subtitle">Zarządzanie infrastrukturą fizyczną</p>
</div>

@if($errors->any())
    <div style="padding: 15px; background: var(--bg-card); border: 1px solid var(--danger); color: var(--danger); margin-bottom: 20px; border-radius: 6px;">
        <strong>Nie można wykonać migracji:</strong> {{ $errors->first() }}
    </div>
@endif

<div class="card">
    
    <form action="{{ route('admin.servers.migrate', $server) }}" method="POST">
        @csrf
        
        <h2 style="font-size: 1.25rem; margin-bottom: 20px; border-bottom: 2px solid var(--border-color); padding-bottom: 10px; display: inline-block;">
            Parametry Instancji
        </h2>

        <div class="migration-stats">
            <div class="stat-box">
                <span class="stat-label">Serwer (Hostname)</span>
                <span class="stat-value">{{ $server->hostname }}</span>
            </div>
            
            <div class="stat-box">
                <span class="stat-label">Wymagany RAM</span>
                <span class="stat-value">{{ $server->plan->ram_mb }} MB</span>
            </div>

            <div class="stat-box">
                <span class="stat-label">Obecny Węzeł</span>
                <span class="stat-value">{{ $server->node->name }}</span>
                <div style="font-size: 0.8rem; margin-top: 2px; color: var(--text-muted);">{{ $server->node->ip_address }}</div>
            </div>
            
            <div class="stat-box">
                <span class="stat-label">Klient</span>
                <span class="stat-value">{{ $server->user->name }}</span>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label class="form-label" for="new_node_id">Wybierz Węzeł Docelowy</label>
            
            <select name="new_node_id" id="new_node_id" class="input-standard" style="padding: 12px;" required>
                <option value="" disabled selected>-- Kliknij, aby wybrać węzeł --</option>
                
                @foreach($nodes as $node)
                    @php
                        // Logika dostępności RAM (liczona w widoku dla podglądu)
                        $usedRam = $node->servers->sum(fn($s) => $s->plan->ram_mb ?? 0);
                        $freeRam = $node->total_ram_mb - $usedRam;
                        $isAvailable = $freeRam >= $server->plan->ram_mb;
                    @endphp

                    <option value="{{ $node->id }}" {{ !$isAvailable ? 'disabled' : '' }}>
                        {{ $node->name }} 
                        (Wolne: {{ number_format($freeRam) }} MB) 
                        &mdash; {{ $node->location->city ?? 'Unknown' }}
                        {{ !$isAvailable ? '[BRAK MIEJSCA]' : '' }}
                    </option>
                @endforeach
            </select>
            <span class="form-hint">Lista zawiera tylko aktywne węzły. Węzły z brakiem wystarczającej pamięci RAM są zablokowane.</span>
        </div>

        <div class="warning-box">
            <div style="font-size: 2rem; font-weight: bold;">!</div>
            <div>
                <strong>Ostrzeżenie o dostępności:</strong><br>
                Proces migracji wiąże się z zatrzymaniem serwera na czas przenoszenia danych. 
                Po zakończeniu operacji publiczny adres IP serwera może ulec zmianie, jeśli konfiguracja sieciowa nowego węzła jest inna.
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 10px;">
            <button type="submit" class="btn btn-primary">
                Potwierdź i Migruj
            </button>
            <a href="{{ route('admin.servers.index') }}" class="btn" style="border: 1px solid var(--border-color); display: inline-flex; align-items: center; text-decoration: none; color: var(--text-main);">
                Anuluj
            </a>
        </div>

    </form>
</div>
@endsection