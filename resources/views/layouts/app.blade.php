<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'VMControlManager') }}</title>
    
    <style>
        :root {
            --bg-body: #f4f6f9;
            --bg-sidebar: #1a202c;
            --bg-card: #ffffff;
            --text-main: #2d3748;
            --text-muted: #718096;
            --primary: #3182ce;
            --primary-hover: #2b6cb0;
            --danger: #e53e3e;
            --success: #38a169;
            --border-color: #e2e8f0;
            
            --font-base: 16px;
            --font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            
            --sidebar-width: 250px;
        }

        body.high-contrast {
            --bg-body: #000000;
            --bg-sidebar: #000000;
            --bg-card: #000000;
            --text-main: #ffff00;
            --text-muted: #00ff00;
            --primary: #ffff00;
            --primary-hover: #ffffff;
            --danger: #ff0000;
            --success: #00ff00;
            --border-color: #ffff00;
        }

        * { box-sizing: border-box; }
        
        body {
            margin: 0;
            font-family: var(--font-family);
            font-size: var(--font-base);
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--bg-sidebar);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            padding: 20px;
            position: fixed;
            height: 100%;
            overflow-y: auto;
        }

        .brand {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 40px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 20px;
        }

        .nav-link {
            display: block;
            padding: 12px 15px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            margin-bottom: 5px;
            border-radius: 4px;
            transition: all 0.2s;
            font-weight: 500;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: #ffffff;
            border-left: 4px solid var(--primary);
        }

        .nav-section {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 40px;
            display: flex;
            flex-direction: column;
        }

        body.no-sidebar .main-content {
            margin-left: 0;
        }

        .top-bar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
        }

        .btn-access {
            background: none;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 5px 12px;
            margin-left: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            border-radius: 4px;
        }
        
        .btn-access:hover { background-color: var(--border-color); }
        body.high-contrast .btn-access { border-color: var(--primary); color: var(--primary); }

        .page-header { margin-bottom: 30px; }
        .page-title { font-size: 1.75rem; font-weight: 600; margin: 0; color: var(--text-main); }
        .page-subtitle { color: var(--text-muted); margin-top: 5px; }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            padding: 25px;
            margin-bottom: 20px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 2px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-primary { background-color: var(--primary); color: #fff; }
        .btn-primary:hover { background-color: var(--primary-hover); }
        
        .btn-danger { background-color: var(--danger); color: #fff; font-size: 0.85rem; padding: 6px 12px; }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-running { background-color: #c6f6d5; color: #22543d; }
        .status-stopped { background-color: #fed7d7; color: #822727; }
        .status-provisioning { background-color: #feebc8; color: #744210; }

        .sr-only {
            position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
            overflow: hidden; clip: rect(0, 0, 0, 0); border: 0;
        }
    </style>
</head>
<body id="main-body" class="{{ (isset($hideSidebar) && $hideSidebar) ? 'no-sidebar' : '' }}">

    @unless(isset($hideSidebar) && $hideSidebar)
    <aside class="sidebar">
        <div class="brand">VM Control</div>
        
        <nav role="navigation" aria-label="Menu główne">
            <div class="nav-section">Panel Klienta</div>
            <a href="{{ route('servers.index') }}" class="nav-link {{ request()->routeIs('servers.index') ? 'active' : '' }}">Moje Serwery</a>
            <a href="#" class="nav-link">Finanse</a>
            <a href="#" class="nav-link">Wsparcie</a>

            @if(auth()->check() && auth()->user()->role === 'admin')
                <div class="nav-section">Administracja</div>
                <a href="{{ route('admin.plans.index') }}" class="nav-link">Plany Hostingowe</a>
                <a href="#" class="nav-link">Węzły (Nodes)</a>
            @endif
        </nav>

        <div style="margin-top: auto;">
             @auth
                <div style="font-size: 0.85rem; opacity: 0.7; margin-bottom: 10px;">
                    Zalogowany: {{ auth()->user()->name }}
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link" style="background:none; border:none; color: rgba(255,255,255,0.7); width: 100%; text-align: left; cursor: pointer; padding-left: 0;">
                        Wyloguj się
                    </button>
                </form>
             @endauth
        </div>
    </aside>
    @endunless
    
    <main class="main-content" role="main">
        
        <div class="top-bar" aria-label="Narzędzia dostępności">
            <button class="btn-access" onclick="toggleContrast()">Kontrast</button>
            <button class="btn-access" onclick="changeFontSize(1)">A+</button>
            <button class="btn-access" onclick="changeFontSize(-1)">A-</button>
        </div>

        @if(session('success'))
            <div role="alert" style="background-color: var(--success); color: white; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div role="alert" style="background-color: var(--danger); color: white; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        function toggleContrast() {
            const body = document.getElementById('main-body');
            body.classList.toggle('high-contrast');
            localStorage.setItem('highContrast', body.classList.contains('high-contrast'));
        }

        function changeFontSize(delta) {
            const root = document.documentElement;
            let currentSize = parseInt(getComputedStyle(root).getPropertyValue('--font-base'));
            let newSize = currentSize + delta;
            if(newSize >= 12 && newSize <= 24) {
                root.style.setProperty('--font-base', newSize + 'px');
                localStorage.setItem('fontSize', newSize);
            }
        }

        if(localStorage.getItem('highContrast') === 'true') document.getElementById('main-body').classList.add('high-contrast');
        if(localStorage.getItem('fontSize')) document.documentElement.style.setProperty('--font-base', localStorage.getItem('fontSize') + 'px');
    </script>
</body>
</html>