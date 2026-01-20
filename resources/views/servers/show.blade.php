@extends('layouts.app')

@section('content')
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
</style>

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

<div class="status-card">
    <div style="display: flex; align-items: center; gap: 20px;">
        <div>
            <span style="display: block; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Status</span>
            
            <span id="status-indicator" 
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

    <div style="display: flex; gap: 10px;">
        <button class="power-btn btn-start" 
                onclick="powerAction('start')" 
                data-url="{{ route('servers.start', $server) }}"
                @disabled($server->status !== 'stopped')>
            Start
        </button>

        <button class="power-btn btn-restart" 
                onclick="powerAction('restart')" 
                data-url="{{ route('servers.restart', $server) }}"
                @disabled($server->status !== 'running')>
            Restart
        </button>

        <button class="power-btn btn-stop" 
                onclick="powerAction('stop')" 
                data-url="{{ route('servers.stop', $server) }}"
                @disabled($server->status !== 'running')>
            Stop
        </button>
    </div>
</div>

<div class="dashboard-grid">
    
    <div class="card">
        <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 1.1rem; border-bottom: 2px solid var(--border-color); padding-bottom: 10px; display: inline-block;">
            Konfiguracja
        </h3>
        <ul class="info-list">
            <li>
                <span class="info-label">System Operacyjny</span>
                <span class="info-value">{{ $server->operatingSystem->name ?? 'Linux' }} {{ $server->operatingSystem->version ?? '' }}</span>
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
        <div style="margin-top: 15px; text-align: right;">
            <a href="#" class="btn btn-primary" style="font-size: 0.9rem;">
                Otwórz Konsolę (Pełny ekran)
            </a>
        </div>
    </div>

</div>
@endsection

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
        <span id="term-uptime"></span> up 0 min,  1 user,  load average: 0.50, 0.20, 0.00<br>
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
</script>