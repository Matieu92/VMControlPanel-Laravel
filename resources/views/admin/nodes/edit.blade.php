@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edytuj Węzeł: {{ $node->name }}</h1>
</div>

<div class="card" style="max-width: 600px;">
    <form action="{{ route('admin.nodes.update', $node) }}" method="POST">
        @csrf
        @method('PUT')
        
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

        <div style="display: flex; gap: 15px;">
            <button type="submit" class="btn btn-primary">Zapisz Zmiany</button>
            <a href="{{ route('admin.nodes.index') }}" class="btn" style="border: 1px solid var(--border-color); color: var(--text-main);">Anuluj</a>
        </div>
    </form>
</div>
@endsection