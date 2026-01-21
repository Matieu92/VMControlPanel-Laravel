@extends('layouts.app')

@section('content')
@push('styles')
<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 20px;
        margin-top: 20px;
    }

    .status-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 20px;
        border-radius: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .power-btn {
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: opacity 0.2s;
        text-decoration: none;
        font-size: 0.9rem;
        color: #fff;
    }
    .power-btn:hover { opacity: 0.9; }

    .power-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        filter: grayscale(100%);
    }

    .btn-start { background-color: #16a34a; }
    .btn-stop  { background-color: #dc2626; }
    .btn-restart { background-color: #d97706; }

    .terminal-window {
        background-color: #0c0c0c;
        border: 1px solid #444;
        font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
        box-shadow: 4px 4px 0px rgba(0,0,0,0.2);
    }

    .terminal-header {
        background-color: #333;
        color: #ddd;
        padding: 5px 10px;
        font-size: 0.85rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #444;
        user-select: none;
    }

    .window-controls {
        display: flex;
        gap: 2px;
    }
    .win-btn {
        width: 14px;
        height: 14px;
        border: 1px solid #888;
        background: #ccc;
        position: relative;
    }

    .win-close::before, .win-close::after {
        content: ''; position: absolute; top: 2px; left: 6px; height: 8px; width: 1px; background: #000;
    }
    .win-close::before { transform: rotate(45deg); }
    .win-close::after { transform: rotate(-45deg); }

    .terminal-body {
        padding: 15px;
        color: #cccccc;
        font-size: 0.95rem;
        min-height: 350px;
        line-height: 1.4;
    }

    .prompt { color: #16a34a; font-weight: bold; }
    .cmd { color: #fff; font-weight: bold; }
    .cursor-block {
        display: inline-block;
        width: 8px;
        height: 16px;
        background-color: #cccccc;
        animation: blink 1s step-end infinite;
        vertical-align: middle;
    }

    @keyframes blink { 50% { opacity: 0; } }

    .info-list { list-style: none; padding: 0; margin: 0; }
    .info-list li {
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
    }
    .info-list li:last-child { border-bottom: none; }
    .info-label { color: var(--text-muted); }
    .info-value { font-weight: 600; color: var(--text-main); }

    body.high-contrast .terminal-window { border: 2px solid #ffff00; }
    body.high-contrast .terminal-body { background-color: #000; color: #ffff00; }
    body.high-contrast .prompt { color: #fff; }
    body.high-contrast .cursor-block { background-color: #ffff00; }
    body.high-contrast .btn-start, 
    body.high-contrast .btn-stop, 
    body.high-contrast .btn-restart {
        background-color: #ffff00 !important;
        color: #000 !important;
        border: 2px solid #000;
    }

    @media (max-width: 900px) {
        .dashboard-grid { grid-template-columns: 1fr; }
    }

    .modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.7);
        display: flex; align-items: center; justify-content: center;
        z-index: 1000;
        backdrop-filter: blur(4px);
    }

    .modal-content {
        width: 100%; max-width: 500px;
        padding: 25px;
        position: relative;
        animation: modalSlideUp 0.3s ease-out;
    }

    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .btn-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted); }

    .os-grid-compact {
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 15px 0;
    }
    .os-option {
        display: flex; align-items: center; gap: 10px; padding: 10px;
        border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer;
    }
    .os-option:hover { border-color: var(--primary); background: var(--bg-body); }
    .os-name { font-weight: 600; font-size: 0.85rem; display: block; }
    .os-ver { font-size: 0.7rem; color: var(--text-muted); }

    .modal-danger-notice {
        background: rgba(220, 53, 69, 0.05); border: 1px solid rgba(220, 53, 69, 0.2);
        padding: 12px; border-radius: 6px; font-size: 0.85rem; color: #dc3545;
    }

    .modal-actions { margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end; }

    @keyframes modalSlideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
@endpush

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">{{ $server->hostname }}</h1>
            <p class="page-subtitle">Panel zarządzania instancją</p>
        </div>
        <div>
             <a href="{{ route('servers.index') }}" class="btn" style="border: 1px solid var(--border-color); color: var(--text-main);">
                &larr; Wróć do listy
             </a>
        </div>
    </div>
</div>

<div class="status-card" role="region" aria-label="Status serwera i szybkie akcje">
    <div style="display: flex; align-items: center; gap: 20px;">
        <div>
            <span style="display: block; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Status</span>
            
            <span id="status-indicator" 
                aria-live="polite"
                style="font-weight: 800; font-size: 1.1rem; color: 
                {{ $server->status === 'running' ? '#16a34a' : ($server->status === 'stopped' ? '#dc2626' : '#d97706') }};">
                
                <span id="status-text">
                    @if($server->status === 'running') RUNNING
                    @elseif($server->status === 'provisioning') INSTALLING
                    @else STOPPED
                    @endif
                    
                </span>
            </span>
        </div>
        
        <div style="height: 40px; width: 1px; background: var(--border-color);"></div>
        
        <div>
            <span style="display: block; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Public IP</span>
            <span style="font-weight: 600; font-size: 1.1rem; color: var(--text-main); font-family: monospace;">
                {{ $server->ip_address ?? '---' }}
            </span>
        </div>
    </div>

    <div style="display: flex; gap: 10px;" role="group" aria-label="Sterowanie zasilaniem">
        <button class="power-btn btn-start" 
                aria-label="Uruchom serwer {{ $server->hostname }}"
                onclick="powerAction('start')" 
                data-url="{{ route('servers.start', $server) }}"
                @disabled($server->status !== 'stopped')>
            Start
        </button>

        <button class="power-btn btn-restart" 
                aria-label="Zrestartuj serwer {{ $server->hostname }}"
                onclick="powerAction('restart')" 
                data-url="{{ route('servers.restart', $server) }}"
                @disabled($server->status !== 'running')>
            Restart
        </button>

        <button class="power-btn btn-stop" 
                aria-label="Wyłącz serwer {{ $server->hostname }}"
                onclick="powerAction('stop')" 
                data-url="{{ route('servers.stop', $server) }}"
                @disabled($server->status !== 'running')>
            Stop
        </button>  
    </div>
</div>

<div class="dashboard-grid">
    
    <div class="card">
        <h2 style="margin-top: 0; margin-bottom: 20px; font-size: 1.1rem; border-bottom: 2px solid var(--border-color); padding-bottom: 10px; display: inline-block;">
            Konfiguracja
        </h2>
        <ul class="info-list">
            <li>
                <span class="info-label">System Operacyjny</span>
                @if($server->status === 'running')
                    <span class="info-value" style="color: var(--text-muted); cursor: not-allowed;" title="Wyłącz serwer najpierw">
                        {{ $server->operatingSystem->name }} {{ $server->operatingSystem->version }}
                    </span>
                @else
                    <span class="info-value clickable" onclick="openReinstallModal()" style="cursor: pointer;">
                        {{ $server->operatingSystem->name }} {{ $server->operatingSystem->version }}
                        <small style="margin-left:5px; color: var(--primary);">[Zmień]</small>
                    </span>
                @endif
            </li>
            <li>
                <span class="info-label">Plan</span>
                <span class="info-value">{{ $server->plan?->name ?? 'Custom' }}</span>
            </li>
            <li>
                <span class="info-label">vCPU</span>
                <span class="info-value">{{ $server->plan?->cpu_cores ?? 0 }} Core</span>
            </li>
            <li>
                <span class="info-label">RAM</span>
                <span class="info-value">{{ ($server->plan?->ram_mb ?? 0) / 1024 }} GB</span>
            </li>
            <li>
                <span class="info-label">Węzeł fizyczny</span>
                <span class="info-value">
                    @if(isset($server->node->location->city))
                        {{ $server->node->location->city }} ({{ $server->node->location->country_code }})
                    @else
                        {{ $server->node->location ?? 'Nieznany' }}
                    @endif
                </span>
            </li>
            <li>
                <span class="info-label">Subskrypcja do</span>
                <span class="info-value" style="color: var(--primary);">
                    {{ $server->subscription ? $server->subscription->ends_at->format('Y-m-d') : '-' }}
                </span>
            </li>
        </ul>
    </div>

    <div>
        <div>
            <div class="terminal-window">
                <div class="terminal-header">
                    <span>{{ 'root@' . ($server->hostname ?: 'server-vps') }}:~</span>
                    
                    <div class="window-controls">
                        <div class="win-btn" title="Minimalizuj"></div>
                        <div class="win-btn" title="Maksymalizuj"></div>
                        <div class="win-btn win-close" title="Zamknij"></div>
                    </div>
                </div>
                <div class="terminal-body">
                </div>
            </div>
        </div>
    </div>

</div>

<div id="reinstallModal" class="modal-overlay" style="display: none;">
    <div class="modal-content card">
        <div class="modal-header">
            <h2 class="h3" style="margin: 0;">Reinstalacja Systemu</h2>
            <button onclick="closeReinstallModal()" class="btn-close">&times;</button>
        </div>

        <form action="{{ route('servers.postReinstall', $server) }}" method="POST">
            @csrf
            <p class="form-hint">Wybierz nowy obraz systemu dla <strong>{{ $server->hostname }}</strong>.</p>

            <div class="os-grid-compact">
                @foreach($server->subscription->plan->operatingSystems as $os)
                    <label class="os-option">
                        <input type="radio" name="operating_system_id" value="{{ $os->id }}" required 
                            {{ $server->operating_system_id == $os->id ? 'checked' : '' }}>
                        <span class="os-name">{{ $os->name }}</span>
                        <span class="os-ver">{{ $os->version }}</span>
                    </label>
                @endforeach
            </div>

            <div class="modal-danger-notice">
                <strong>Uwaga:</strong> Wszystkie dane na serwerze zostaną trwale usunięte.
                <label style="display: flex; align-items: center; gap: 8px; margin-top: 10px; cursor: pointer;">
                    <input type="checkbox" required> <span style="font-size: 0.8rem;">Potwierdzam usunięcie danych</span>
                </label>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn btn-danger">Rozpocznij</button>
                <button type="button" onclick="closeReinstallModal()" class="btn">Anuluj</button>
            </div>
        </form>
    </div>
</div>

@endsection

@php
    $uptime = $server->status === 'running' ? $server->updated_at->diffForHumans(null, true) : '0 min';
@endphp

@push('scripts')
<script>
    const terminalRunningTemplate = `
        Welcome to {{ $server->operatingSystem->name ?? 'Linux' }} {{ $server->operatingSystem->version ?? '' }} LTS<br>
        <br>
        System information as of <span id="term-date"></span><br>
        <br>
        System load:  0.02              Processes:       112<br>
        Usage of /:   14.2% of 48.00GB  Users logged in: 0<br>
        Memory usage: 12%               IPv4 address:    {{ $server->ip_address ?? '---' }}<br>
        <br>
        <span class="prompt">{{ 'root@' . ($server->hostname ?: 'vps') }}:~#</span> <span class="cmd">uptime</span><br>
        <span id="term-uptime"></span> up {{ $uptime }},  1 user,  load average: 0.50, 0.20, 0.00<br>
        <span class="prompt">{{ 'root@' . ($server->hostname ?: 'vps') }}:~#</span> <span class="cursor-block"></span>
    `;

    const terminalStoppedTemplate = `
        <br><br>
        <div style="color: #666; text-align: center;">Connection closed by remote host.</div>
    `;

    function updateTerminalContent(status) {
        const termBody = document.querySelector('.terminal-body');
        const now = new Date();

        if (status === 'running') {
            termBody.innerHTML = terminalRunningTemplate;
            if(document.getElementById('term-date')) 
                document.getElementById('term-date').innerText = now.toUTCString();
            if(document.getElementById('term-uptime'))
                document.getElementById('term-uptime').innerText = now.toLocaleTimeString();
        } 
        else if (status === 'stopped') {
            termBody.innerHTML = terminalStoppedTemplate;
        }
    }

    function updateButtonsState(status) {
        const btnStart = document.querySelector('.btn-start');
        const btnStop = document.querySelector('.btn-stop');
        const btnRestart = document.querySelector('.btn-restart');

        if (status === 'running') {
            btnStart.disabled = true;
            btnStop.disabled = false;
            btnRestart.disabled = false;
        } else if (status === 'stopped') {
            btnStart.disabled = false;
            btnStop.disabled = true;
            btnRestart.disabled = true;
        } else {
            btnStart.disabled = true;
            btnStop.disabled = true;
            btnRestart.disabled = true;
        }
    }

    function powerAction(action) {
        const btnSelector = action === 'start' ? '.btn-start' : (action === 'stop' ? '.btn-stop' : '.btn-restart');
        const btn = document.querySelector(btnSelector);
        const url = btn.dataset.url;
        
        const statusText = document.getElementById('status-text');
        const statusIndicator = document.getElementById('status-indicator');
        const termBody = document.querySelector('.terminal-body');
        
        let loadingText = '';
        switch(action) {
            case 'start': 
                loadingText = 'STARTING...'; 
                termBody.innerHTML = '<br>Booting system...<br>_ <span class="cursor-block"></span>';
                break;
            case 'stop':  
                loadingText = 'STOPPING...'; 
                break;
            case 'restart': 
                loadingText = 'RESTARTING...';
                break;
        }

        statusText.innerText = loadingText;
        statusIndicator.style.color = '#d97706';
        document.querySelectorAll('.power-btn').forEach(b => b.disabled = true);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'running') {
                statusText.innerText = 'RUNNING';
                statusIndicator.style.color = '#16a34a';
            } else if (data.status === 'stopped') {
                statusText.innerText = 'STOPPED';
                statusIndicator.style.color = '#dc2626';
            }

            updateButtonsState(data.status);
            updateTerminalContent(data.status);
        })
        .catch(error => {
            console.error('Error:', error);
            statusText.innerText = 'ERROR';
            alert('Błąd połączenia.');
            location.reload(); 
        });
    }

    function openReinstallModal() {
    document.getElementById('reinstallModal').style.display = 'flex';
    }

    function closeReinstallModal() {
        document.getElementById('reinstallModal').style.display = 'none';
    }

    window.onclick = function(event) {
        let modal = document.getElementById('reinstallModal');
        if (event.target == modal) {
            closeReinstallModal();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
    const currentStatus = '{{ $server->status }}';
    
    updateTerminalContent(currentStatus);
    updateButtonsState(currentStatus);
    });
</script>
@endpush