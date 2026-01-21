@extends('layouts.app')

@section('content')
<style>
    .btn-stop  { 
        background-color: #dc2626; 
        color: #fff;
    }

    body.high-contrast .btn-stop {
        font-weight: 1000;
    }
</style>
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="page-title">Węzły Fizyczne (Nodes)</h1>
        <a href="{{ route('admin.nodes.create') }}" class="btn btn-primary">Dodaj Węzeł</a>
    </div>
</div>

@if($errors->any())
    <div style="color: var(--danger); margin-bottom: 20px; padding: 10px; border: 1px solid var(--danger);">
        {{ $errors->first() }}
    </div>
@endif

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nazwa</th>
                <th>Adres IP</th>
                <th>Lokalizacja</th>
                <th>RAM (Total)</th>
                <th>CPU (Rdzenie)</th>
                <th>Status</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nodes as $node)
            <tr>
                <td><strong>{{ $node->name }}</strong></td>
                <td style="font-family: monospace;">{{ $node->ip_address }}</td>
                <td>
                    {{ $node->location->city ?? '-' }} ({{ $node->location->country_code ?? '-' }})
                </td>
                <td>{{ number_format($node->total_ram_mb / 1024, 1) }} GB</td>
                <td>{{ $node->total_cpu_cores }}</td>
                <td>
                    @if($node->is_active)
                        <span style="color: #16a34a; font-weight: bold;">Aktywny</span>
                    @else
                        <span style="color: var(--danger); font-weight: bold;">Nieaktywny</span>
                    @endif
                </td>
                <td style="display: flex; gap: 10px;">
                    <a href="{{ route('admin.nodes.edit', $node) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem;"> Edytuj</a>
                    
                    <form action="{{ route('admin.nodes.destroy', $node) }}" method="POST" onsubmit="return confirm('Czy na pewno usunąć ten węzeł?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-stop" style="padding: 6px 12px; font-size: 0.85rem;">Usuń</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection