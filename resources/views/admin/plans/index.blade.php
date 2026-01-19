@extends('layouts.app')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Plany Hostingowe</h1>
            <p class="page-subtitle">Definiuj zasoby i przypisuj dostępne systemy operacyjne</p>
        </div>
        <button class="btn btn-primary" disabled>+ Nowy Plan</button>
    </div>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th scope="col">Nazwa Planu</th>
                <th scope="col">Cena (PLN)</th>
                <th scope="col">Zasoby (RAM / CPU)</th>
                <th scope="col">Dostępne Systemy (Relacja N:M)</th>
                <th scope="col">Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plans as $plan)
            <tr>
                <td><strong>{{ $plan->name }}</strong></td>
                <td>{{ number_format($plan->price, 2) }} zł</td>
                <td>
                    <span style="display: block;">RAM: {{ $plan->ram_mb }} MB</span>
                    <small style="color: var(--text-muted);">CPU: {{ $plan->cpu_cores }} Core</small>
                </td>
                <td>
                    @forelse($plan->operatingSystems as $os)
                        <span style="display: inline-block; background: var(--bg-body); border: 1px solid var(--border-color); padding: 2px 6px; border-radius: 4px; font-size: 0.8rem; margin-right: 4px; margin-bottom: 4px;">
                            {{ $os->name }}
                        </span>
                    @empty
                        <span style="color: var(--danger); font-size: 0.85rem;">Brak przypisanych systemów!</span>
                    @endforelse
                </td>
                <td>
                    <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">
                        Edytuj
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection