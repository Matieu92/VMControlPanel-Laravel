@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Nowy Plan</h1>
</div>

<div class="card" style="max-width: 600px;">
    <form action="{{ route('admin.plans.store') }}" method="POST">
        @csrf
        
        <div style="margin-bottom: 15px;">
            <label class="form-label">Nazwa Planu</label>
            <input type="text" name="name" class="input-standard" placeholder="np. Standard VPS" required>
        </div>

        <div>
            <div>
                <label class="form-label">Cena (zł/mc)</label>
                <input type="number" step="0.01" name="price" class="input-standard" placeholder="np. 21" required><p>
            </div>
            <div>
                <label class="form-label">RAM (MB)</label>
                <input type="number" name="ram_mb" class="input-standard" placeholder="np. 2048" required><p>
            </div>
            <div>
                <label class="form-label">CPU (Rdzenie)</label>
                <input type="number" name="cpu_cores" class="input-standard" placeholder="np. 2" required><p>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Zapisz Plan</button>
    </form>
</div>
@endsection