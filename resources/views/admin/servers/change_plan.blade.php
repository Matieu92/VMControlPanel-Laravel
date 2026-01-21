@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Zmiana Planu</h1>
    <p class="page-subtitle">Instancja: {{ $server->hostname }} | System: {{ $server->operatingSystem->name }}</p>
</div>

@if($errors->has('server_plan_id'))
    <div style="padding: 15px; background: rgba(220, 53, 69, 0.1); border: 1px solid var(--danger); color: var(--danger); border-radius: 6px; margin-bottom: 20px;">
        {{ $errors->first('server_plan_id') }}
    </div>
@endif

<div class="card" style="max-width: 650px;">
    <form action="{{ route('admin.servers.update_plan', $server) }}" method="POST" id="planForm">
        @csrf
        @method('PATCH')

        <div style="margin-bottom: 20px;">
            <label class="form-label">Wybierz nowy plan zasobów</label>
            <select name="server_plan_id" id="planSelect" class="input-standard" style="padding: 12px;">
                @foreach($plans as $plan)
                    @php
                        // Sprawdzamy kompatybilność "w locie" dla widoku
                        $isCompatible = $plan->operatingSystems->contains($server->operating_system_id);
                    @endphp
                    <option value="{{ $plan->id }}" 
                            data-compatible="{{ $isCompatible ? '1' : '0' }}"
                            {{ $server->subscription->server_plan_id == $plan->id ? 'selected' : '' }}>
                        {{ $plan->name }} ({{ $plan->ram_mb }}MB RAM) 
                        {{ !$isCompatible ? 'NIEKOMPATYBILNY' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div id="compatWarning" style="display: none; padding: 20px; background: #fff5f5; border-left: 5px solid var(--danger); margin-bottom: 25px;">
            <strong style="color: var(--danger); display: block; margin-bottom: 10px;">Problem z kompatybilnością</strong>
            <p style="margin: 0; font-size: 0.9rem; color: #66101f;">
                Wybrany plan nie obsługuje systemu <strong>{{ $server->operatingSystem->name }}</strong>. 
                Zapisanie tych zmian może spowodować niestabilność usługi.
            </p>
            <ul style="margin: 10px 0 0 20px; font-size: 0.85rem; color: #66101f;">
                <li>Zalecane rozwiązanie: Wybierz plan oznaczony jako kompatybilny.</li>
                <li>Alternatywa: Poproś klienta o reinstalację systemu na lżejszy.</li>
            </ul>
        </div>

        <div style="display: flex; gap: 15px;">
            <button type="submit" id="submitBtn" class="btn btn-primary">Zapisz zmiany</button>
            <a href="{{ route('admin.servers.index') }}" class="btn" style="border: 1px solid var(--border-color); color: var(--text-main);">Wróć</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('planSelect');
    const warning = document.getElementById('compatWarning');
    const btn = document.getElementById('submitBtn');

    function checkCompatibility() {
        const selectedOption = select.options[select.selectedIndex];
        const isCompatible = selectedOption.getAttribute('data-compatible') === '1';

        if (!isCompatible) {
            warning.style.display = 'block';
            btn.style.opacity = '0.6';
            btn.innerText = 'Zapisz mimo ostrzeżenia';
        } else {
            warning.style.display = 'none';
            btn.style.opacity = '1';
            btn.innerText = 'Zapisz zmiany';
        }
    }

    select.addEventListener('change', checkCompatibility);
    checkCompatibility();
});
</script>
@endpush
