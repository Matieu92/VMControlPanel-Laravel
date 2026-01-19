@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Nowa Instancja</h1>
    <p class="page-subtitle">Skonfiguruj parametry swojego nowego serwera VPS</p>
</div>

<div class="card" style="max-width: 800px;">
    
    @if($errors->any())
        <div style="background-color: #fff5f5; border-left: 4px solid var(--danger); padding: 15px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px; color: var(--danger);">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('servers.store') }}" method="POST">
        @csrf
        
        <div style="margin-bottom: 25px;">
            <label for="hostname" style="display: block; font-weight: 600; margin-bottom: 8px;">Nazwa Hosta (Hostname)</label>
            <input type="text" name="hostname" id="hostname" class="form-control" placeholder="np. web-production-01" required
                   style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 1rem;">
            <small style="color: var(--text-muted);">Unikalna nazwa identyfikująca Twój serwer w sieci.</small>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
            <div>
                <label for="server_plan_id" style="display: block; font-weight: 600; margin-bottom: 8px;">Wybierz Plan</label>
                <select name="server_plan_id" id="server_plan_id" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 1rem; background: var(--bg-card); color: var(--text-main);">
                    @foreach(\App\Models\ServerPlan::all() as $plan)
                        <option value="{{ $plan->id }}">
                            {{ $plan->name }} ({{ $plan->ram_mb }} MB RAM - {{ $plan->price }} PLN)
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="operating_system_id" style="display: block; font-weight: 600; margin-bottom: 8px;">System Operacyjny</label>
                <select name="operating_system_id" id="operating_system_id" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 1rem; background: var(--bg-card); color: var(--text-main);">
                    @foreach(\App\Models\OperatingSystem::all() as $os)
                        <option value="{{ $os->id }}">{{ $os->name }} {{ $os->version }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="border-top: 1px solid var(--border-color); padding-top: 20px; text-align: right;">
            <a href="{{ route('servers.index') }}" style="color: var(--text-muted); text-decoration: none; margin-right: 20px;">Anuluj</a>
            <button type="submit" class="btn btn-primary">Uruchom Serwer</button>
        </div>
    </form>
</div>
@endsection