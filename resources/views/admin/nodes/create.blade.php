@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Nowy Węzeł</h1>
</div>

<div class="card" style="max-width: 600px;">
    <form action="{{ route('admin.nodes.store') }}" method="POST">
        @csrf
        
        <div style="margin-bottom: 15px;">
            <label class="form-label">Nazwa Węzła</label>
            <input type="text" name="name" class="input-standard" placeholder="np. Node-WAW-01" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label class="form-label">Adres IP (Publiczny)</label>
            <input type="text" name="ip_address" class="input-standard" placeholder="np. 192.168.1.100" required>
        </div>

        <div>
            <div>
                <label class="form-label">Miasto</label>
                <input type="text" name="city" class="input-standard" placeholder="np. Warszawa" required><p>
            </div>
            <div>
                <label class="form-label">Kod Kraju (2 litery)</label>
                <input type="text" name="country_code" class="input-standard" placeholder="np. PL" maxlength="2" style="text-transform: uppercase;" required><p>
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <label class="form-label">Całkowita pamięć RAM (MB)</label>
            <input type="number" name="total_ram_mb" class="input-standard" placeholder="np. 32768 (dla 32GB)" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label class="form-label">Całkowita liczba rdzeni CPU</label>
            <input type="number" name="total_cpu_cores" class="input-standard" placeholder="np. 16" required> 
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" checked style="width: 20px; height: 20px;">
                <span style="font-weight: 600;">Węzeł Aktywny (dostępny do alokacji)</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Dodaj Węzeł</button>
    </form>
</div>
@endsection