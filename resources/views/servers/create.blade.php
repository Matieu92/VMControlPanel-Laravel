@extends('layouts.app')

@section('content')
<style>
    .form-section { margin-bottom: 35px; }
    .form-label { display: block; margin-bottom: 10px; font-weight: 600; color: var(--text-main); }
    .form-hint { font-size: 0.85rem; color: var(--text-muted); display: block; margin-top: 5px; }
    
    .input-standard {
        width: 100%; padding: 10px 12px; font-size: 1rem;
        border: 1px solid var(--border-color); background: var(--bg-body); color: var(--text-main);
        border-radius: 6px;
    }
    
    .os-family-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 15px;
    }

    .family-card-label { cursor: pointer; position: relative; }
    .family-input { position: absolute; opacity: 0; width: 0; height: 0; }
    
    .family-card-content {
        background: var(--bg-card); border: 1px solid var(--border-color);
        border-radius: 8px; padding: 15px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 10px; transition: all 0.2s ease; height: 100px;
    }

    .family-icon {
        width: 40px; height: 40px; border-radius: 50%;
        background: var(--bg-body); border: 1px solid var(--border-color);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: var(--text-main); font-size: 1.2rem;
    }

    .family-name { font-weight: 600; color: var(--text-main); }

    .family-input:checked + .family-card-content {
        border-color: var(--primary); background-color: var(--bg-body);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .family-input:checked + .family-card-content .family-icon {
        background-color: var(--primary); color: #fff; border-color: var(--primary);
    }

    .versions-container {
        margin-top: 20px;
        padding: 20px;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        display: none;
    }
    .versions-container.active { display: block; animation: fadeIn 0.3s ease; }

    .versions-list {
        display: flex; flex-wrap: wrap; gap: 10px;
    }

    .version-label { cursor: pointer; position: relative; }
    .version-input { position: absolute; opacity: 0; width: 0; height: 0; }
    
    .version-btn {
        padding: 8px 20px;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        font-weight: 500;
        color: var(--text-main);
        transition: all 0.2s;
        display: flex; align-items: center; gap: 8px;
    }
    
    .version-input:checked + .version-btn {
        background: var(--primary); color: #fff; border-color: var(--primary);
    }
    .version-input:focus + .version-btn {
        outline: 2px solid var(--primary); outline-offset: 2px;
    }

    .plans-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px;
    }
    .plan-card-content {
        background: var(--bg-card); border: 1px solid var(--border-color);
        border-radius: 6px; padding: 20px; height: 100%;
        display: flex; flex-direction: column; justify-content: space-between;
        cursor: pointer;
    }
    .plan-input { position: absolute; opacity: 0; }
    
    .plan-input:checked + .plan-card-content {
        border-color: var(--primary); box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    body.high-contrast .family-input:checked + .family-card-content,
    body.high-contrast .version-input:checked + .version-btn,
    body.high-contrast .plan-input:checked + .plan-card-content {
        border: 3px solid #ffff00; background-color: #000; color: #ffff00;
    }

    body.high-contrast .family-input:checked + .family-card-content .family-icon {
        background-color: #ffff00 !important;
        color: #000000 !important;
        border-color: #ffff00 !important;
    }
    
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
</style>

@php
    $groupedSystems = $osList->groupBy('name');
@endphp

<div class="page-header">
    <h1 class="page-title">Konfigurator Serwera</h1>
    <p class="page-subtitle">Dostosuj parametry swojej nowej instancji wirtualnej</p>
</div>

@if ($errors->any())
    <div style="background-color: var(--bg-card); border: 1px solid var(--danger); color: var(--danger); padding: 15px; border-radius: 6px; margin-bottom: 20px;">
        <strong>Popraw formularz:</strong>
        <ul style="margin: 5px 0 0 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('servers.store') }}" method="POST">
    @csrf

    <div class="card" style="padding: 30px;">
        
        <div class="form-section">
            <label for="hostname" class="form-label">Nazwa Hosta</label>
            <input type="text" id="hostname" name="hostname" class="input-standard" 
                   placeholder="np. web-server-01" value="{{ old('hostname') }}" required>
            @error('hostname') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">

        <div class="form-section">
            <label class="form-label">Wybierz System Operacyjny</label>
            
            <div class="os-family-grid" role="radiogroup">
                @foreach($groupedSystems as $osName => $versions)
                <label class="family-card-label" onclick="showVersions('{{ Str::slug($osName) }}')">
                    <input type="radio" name="os_family" class="family-input">
                    
                    <div class="family-card-content">
                        <div class="family-icon">{{ substr($osName, 0, 1) }}</div>
                        <span class="family-name">{{ $osName }}</span>
                    </div>
                </label>
                @endforeach
            </div>

            @foreach($groupedSystems as $osName => $versions)
            <div id="versions-{{ Str::slug($osName) }}" class="versions-container">
                <span style="display: block; margin-bottom: 10px; font-weight: 600; font-size: 0.9rem;">
                    Dostępne wersje dla {{ $osName }}:
                </span>
                
                <div class="versions-list">
                    @foreach($versions as $os)
                    <label class="version-label">
                        <input type="radio" name="operating_system_id" value="{{ $os->id }}" 
                               class="version-input" 
                               {{ old('operating_system_id') == $os->id ? 'checked' : '' }}
                               required>
                        <span class="version-btn">{{ $os->version }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
            
            @error('operating_system_id')
                <span style="color: var(--danger); font-size: 0.85rem; display: block; margin-top: 10px;">
                    Musisz wybrać konkretną wersję systemu.
                </span>
            @enderror
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">

        <div class="form-section">
            <label class="form-label">Plan Zasobów</label>
            <div class="plans-grid" role="radiogroup">
                @foreach($plans as $plan)

                @php
                    $allowedSystemIds = $plan->operatingSystems->pluck('id')->toJson();
                @endphp

                <label class="family-card-label plan-item" data-allowed-systems="{{ $allowedSystemIds }}">                    <input type="radio" name="server_plan_id" value="{{ $plan->id }}" 
                           class="plan-input" {{ old('server_plan_id') == $plan->id ? 'checked' : '' }} required>
                    
                    <div class="plan-card-content">
                        <div style="text-align: center;">
                            <span style="font-weight: 700; display: block;">{{ $plan->name }}</span>
                            <span style="color: var(--primary); font-weight: 800; font-size: 1.3rem;">
                                {{ number_format($plan->price, 0) }} zł
                            </span>
                        </div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 15px; border-top: 1px solid var(--border-color); padding-top: 10px;">
                            <div>RAM: <strong>{{ $plan->ram_mb / 1024 }} GB</strong></div>
                            <div>CPU: <strong>{{ $plan->cpu_cores }} vCore</strong></div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            <div id="no-plans-message" style="display: none; padding: 20px; text-align: center; color: var(--danger); border: 1px dashed var(--danger); border-radius: 8px; margin-top: 15px;">
                Brak dostępnych planów dla wybranego systemu operacyjnego.
            </div>

            @error('server_plan_id') <span style="color: var(--danger); font-size: 0.85rem;">Wybierz plan.</span> @enderror
        </div>

        <div style="margin-top: 40px; display: flex; gap: 15px;">
            <button type="submit" class="btn btn-primary">Utwórz Serwer</button>
            <a href="{{ route('servers.index') }}" class="btn" style="border: 1px solid var(--border-color);">Anuluj</a>
        </div>
    </div>
</form>

<script>
    function showVersions(slug) {
        document.querySelectorAll('.versions-container').forEach(el => {
            el.classList.remove('active');
        });

        const target = document.getElementById('versions-' + slug);
        if (target) {
            target.classList.add('active');
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const osInputs = document.querySelectorAll('input[name="operating_system_id"]');
        const plans = document.querySelectorAll('.plan-item');
        const noPlansMsg = document.getElementById('no-plans-message');

        function filterPlans(selectedOsId) {
            let visibleCount = 0;
            
            selectedOsId = String(selectedOsId);

            plans.forEach(plan => {
                const allowedSystems = JSON.parse(plan.dataset.allowedSystems);
                const isAvailable = allowedSystems.length === 0 || allowedSystems.includes(parseInt(selectedOsId));

                if (isAvailable) {
                    plan.style.display = 'block';
                    visibleCount++;
                } else {
                    plan.style.display = 'none';
                    const input = plan.querySelector('input');
                    if(input.checked) input.checked = false;
                }
            });

            noPlansMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        osInputs.forEach(input => {
            input.addEventListener('change', function() {
                filterPlans(this.value);
            });
        });

        const checkedOs = document.querySelector('input[name="operating_system_id"]:checked');
        if (checkedOs) {
            filterPlans(checkedOs.value);
            const container = checkedOs.closest('.versions-container');
            if(container) container.classList.add('active');
        }
    });
</script>
@endsection