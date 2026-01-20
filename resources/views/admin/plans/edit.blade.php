@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edytuj Plan: {{ $plan->name }}</h1>
</div>

@if ($errors->any())
    <div style="background-color: rgba(220, 53, 69, 0.1); border: 1px solid var(--danger); color: var(--danger); padding: 15px; border-radius: 6px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card" style="max-width: 600px;">
    <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
        @csrf
        @method('PUT') <div style="margin-bottom: 15px;">
            <label class="form-label">Nazwa Planu</label>
            <input type="text" name="name" class="input-standard" 
                   value="{{ old('name', $plan->name) }}" required>
        </div>

        <div>
            <div>
                <label class="form-label">Cena (zł/mc)</label>
                <input type="number" step="0.01" name="price" class="input-standard" 
                       value="{{ old('price', $plan->price) }}" required><p>
            </div>
            <div>
                <label class="form-label">RAM (MB)</label>
                <input type="number" name="ram_mb" class="input-standard" 
                       value="{{ old('ram_mb', $plan->ram_mb) }}" required><p>
            </div>
            <div>
                <label class="form-label">CPU (Rdzenie)</label>
                <input type="number" name="cpu_cores" class="input-standard" 
                       value="{{ old('cpu_cores', $plan->cpu_cores) }}" required><p>
            </div>
        </div>
        <div style="display: flex; gap: 15px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
            <a href="{{ route('admin.plans.index') }}" class="btn" style="border: 1px solid var(--border-color); color: var(--text-main);">Anuluj</a>
        </div>
    </form>
</div>
@endsection