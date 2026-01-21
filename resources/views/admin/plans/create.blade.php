@extends('layouts.app')

@section('content')
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
<div class="page-header">
    <h1 class="page-title">Nowy Plan</h1>
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
    <form action="{{ route('admin.plans.store') }}" method="POST">
        @csrf

        <h3 class="h3" style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            Parametry Planu
        </h3>

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

            <hr></hr>

            <p class="form-hint" style="margin-bottom: 15px;">
                Odznacz systemy, które NIE POWINNY być dostępne dla tego planu (np. zbyt wymagające).
            </p>

            <div class="systems-grid">
                @foreach($systems as $system)
                <label class="system-checkbox-card">
                    <input type="checkbox" name="systems[]" value="{{ $system->id }}" class="system-checkbox" checked>
                    <div class="system-info-mini">
                        <span class="system-name-mini">{{ $system->name }}</span>
                        <span class="system-version-mini">{{ $system->version }}</span>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <div style="margin-top: 40px; display: flex; gap: 15px;">
            <button type="submit" class="btn btn-primary">Dodaj Plan</button>
            <a href="{{ route('admin.plans.index') }}" class="btn" style="border: 1px solid var(--border-color); color: var(--text-main);">Anuluj</a>
        </div>
    </form>
</div>
@endsection