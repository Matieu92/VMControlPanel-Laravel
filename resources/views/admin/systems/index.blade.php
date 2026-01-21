@extends('layouts.app')

@section('content')
@push('styles')
<style>
    .btn-stop  { 
        background-color: #dc2626; 
        color: #fff;
    }

    body.high-contrast .btn-stop {
        font-weight: 1000;
    }
</style>
@endpush
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="page-title">Systemy Operacyjne</h1>
        <a href="{{ route('admin.systems.create') }}" class="btn btn-primary">Dodaj System</a>
    </div>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nazwa</th>
                <th>Wersja</th>
                <th>Podgląd ikony</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($systems as $system)
            <tr>
                <td><strong>{{ $system->name }}</strong></td>
                <td>{{ $system->version }}</td>
                <td>
                    <div style="width: 32px; height: 32px; background: var(--bg-body); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; border: 1px solid var(--border-color);">
                        {{ substr($system->name, 0, 1) }}
                    </div>
                </td>
                <td>
                    <form action="{{ route('admin.systems.destroy', $system) }}" method="POST" onsubmit="return confirm('Usunąć?');">
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