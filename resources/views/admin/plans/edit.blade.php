@extends('layouts.app')

@section('content')
@push('styles')
<style>
    .systems-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); 
        gap: 10px;
        margin-top: 10px;
    }

    .system-checkbox-card {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        min-height: 45px;
    }

    .system-checkbox-card:hover {
        border-color: var(--primary);
        background: var(--bg-card);
    }

    .system-checkbox {
        width: 16px; 
        height: 16px;
        margin: 0;
    }

    .system-info-mini {
        line-height: 1.2;
    }

    .system-name-mini {
        font-size: 0.85rem;
        font-weight: 600;
        display: block;
    }

    .system-version-mini {
        font-size: 0.7rem;
        color: var(--text-muted);
    }
</style>
@endpush

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
 
        <hr>

        <p class="form-hint" style="margin-bottom: 15px;">
            Zaznacz systemy operacyjne, które mogą być zainstalowane na tym planie. Np. cięższe systemy (Windows) mogą wymagać mocniejszych planów.
        </p>

        <div class="systems-grid">
            @foreach($systems as $system)
                @php
                    $isChecked = $plan->operatingSystems->contains($system->id);
                @endphp

            <label class="system-checkbox-card">
                <input type="checkbox" name="systems[]" value="{{ $system->id }}" 
                    class="system-checkbox" {{ $isChecked ? 'checked' : '' }}>
                <span class="system-info-mini">
                    <span class="system-name-mini">{{ $system->name }}</span>
                    <span class="system-version-mini">{{ $system->version }}</span>
                </span>
            </label>
            @endforeach
        </div>

        <div style="margin-top: 40px; display: flex; gap: 15px;">
            <button type="submit" class="btn btn-primary">Zapisz Zmiany</button>
            <a href="{{ route('admin.plans.index') }}" class="btn" style="border: 1px solid var(--border-color); color: var(--text-main);">Anuluj</a>
        </div>
    </form>
</div>
@endsection