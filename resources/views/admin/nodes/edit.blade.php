@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edytuj Węzeł: {{ $node->name }}</h1>
    <p class="page-subtitle">Zarządzanie konfiguracją fizyczną oraz przypisanymi instancjami VPS.</p>
</div>

<form action="{{ route('admin.nodes.update', $node) }}" method="POST">
    @csrf
    @method('PUT')

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: start;">
        
        <div class="card">
            <h2 style="font-size: 1.1rem; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Parametry Techniczne</h2>
            
        <div style="margin-bottom: 15px;">
            <label class="form-label">Nazwa Węzła</label>
            <input type="text" name="name" class="input-standard"
                   value="{{ old('name', $node->name) }}" required>
        </div>
        <div style="margin-bottom: 15px;">
            <label class="form-label">Adres IP</label>
            <input type="text" name="ip_address" class="input-standard"
                   value="{{ old('ip_address', $node->ip_address) }}" required>
        </div>
        <div>
            <div>
                <label class="form-label">Miasto</label>
                <input type="text" name="city" class="input-standard"
                       value="{{ old('city', $node->location->city ?? '') }}" required><p>
            </div>
            <div>
                <label class="form-label">Kod Kraju</label>
                <input type="text" name="country_code" class="input-standard" maxlength="2" style="text-transform: uppercase;"
                       value="{{ old('country_code', $node->location->country_code ?? '') }}" required><p>
            </div>
        </div>
        <div style="margin-bottom: 15px;">
            <label class="form-label">Całkowita pamięć RAM (MB)</label>
            <input type="number" name="total_ram_mb" class="input-standard"
                   value="{{ old('total_ram_mb', $node->total_ram_mb) }}" required>
        </div>
        <div style="margin-bottom: 15px;">
            <label class="form-label">Całkowita liczba rdzeni CPU</label>
            <input type="number" name="total_cpu_cores" class="input-standard"
                   value="{{ old('total_cpu_cores', $node->total_cpu_cores) }}" required>
        </div>
        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" style="width: 20px; height: 20px;"
                       {{ $node->is_active ? 'checked' : '' }}>
                <span style="font-weight: 600;">Węzeł Aktywny</span>
            </label>
        </div>
    </div>
        <div class="card">
            <h2 style="font-size: 1.1rem; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Serwery w tym Węźle</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 15px;">
                Zaznacz serwery, które mają zostać przypisane do tego węzła. Odznaczenie serwera spowoduje jego odpięcie (wymaga ręcznego przypisania do innego węzła).
            </p>

            <div style="max-height: 400px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 4px;">
                <table class="data-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Status</th>
                            <th>Nazwa / Hostname</th>
                            <th>Aktualny Węzeł</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($servers as $server)
                        <tr>
                            <td>
                                <input type="checkbox" name="assigned_servers[]" value="{{ $server->id }}"
                                       id="server_{{ $server->id }}"
                                       {{ $server->node_id == $node->id ? 'checked' : '' }}
                                       aria-label="Przypisz serwer {{ $server->hostname }} do tego węzła">
                            </td>
                            <td>
                                <label for="server_{{ $server->id }}" style="cursor: pointer;">
                                    <strong>{{ $server->hostname }}</strong><br>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $server->ip_address }}</span>
                                </label>
                            </td>
                            <td>
                                @if($server->node)
                                <span style="font-size: 0.85rem;">{{ $server->node->name }}</span>
                                @else
                                <span style="color: var(--danger); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 2px 6px;">
                                    Nieprzypisany
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 15px; margin-top: 20px;">
        <button type="submit" class="btn btn-primary">Zapisz Zmiany Konfiguracyjne</button>
        <a href="{{ route('admin.nodes.index') }}" class="btn" style="border: 1px solid var(--border-color); color: var(--text-main);">Powrót do Listy</a>
    </div>
</form>
@endsection