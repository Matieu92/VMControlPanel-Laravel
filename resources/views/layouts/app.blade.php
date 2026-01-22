<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Profesjonalne zarządzanie serwerami VPS i chmurą Tier III. Pełna izolacja zasobów KVM, wysoka wydajność oraz zgodność z dostępnością cyfrową WCAG 2.1.">
    <meta name="author" content="Mateusz Brodzik 21219">
    <title>{{ config('app.name', 'VMControlManager') }}</title> 
    @stack('styles')
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

        body.high-contrast .btn-primary,
        body.high-contrast .btn-access {
            background-color: #ffff00 !important;
            color: #000000 !important;
            font-weight: 800;
            border: 1px solid #000000;
        }
        
        body.high-contrast .btn-primary:hover,
        body.high-contrast .btn-access:hover {
            background-color: #ffffff !important; 
            color: #000000 !important;
            border-color: #000000;
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

        .brand a, 
        .brand a:visited, 
        .brand a:hover, 
        .brand a:active {
            color: inherit;
            text-decoration: none;
            outline: none;
            display: block;
        }

        .brand a:hover {
            opacity: 0.8;
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

        .high-contrast .nav-section {
            color: rgb(0, 255, 0);
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
            background: #e7e7e7;
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
            font-size: 1rem; 
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
            font-size: 1rem; 
            color: var(--text-main);
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
        
        input, select, textarea, button, optgroup, option {
            font-family: inherit;
            font-size: 100%;
            line-height: 1.15;
        }

        .form-control, 
        .form-select, 
        .btn, 
        .input-standard,
        .form-label,
        .input-group-text {
            font-size: 1rem !important;
        }

        body.high-contrast .form-label {
            font-weight: bold;
            color: var(--text-muted);
        }

        h1, .h1, .page-title { font-size: 2.5rem !important; }
        h2, .h2 { font-size: 2rem !important; }
        h3, .h3 { font-size: 1.75rem !important; }
        
        .input-giant {
            font-size: 1.5rem !important;
        }
        
        small, .text-small, .form-hint {
            font-size: 0.875rem !important;
        }

        body.high-contrast input,
        body.high-contrast select,
        body.high-contrast textarea,
        body.high-contrast .form-control,
        body.high-contrast .form-select,
        body.high-contrast .input-standard {
            background-color: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
        }

        body.high-contrast input:focus,
        body.high-contrast select:focus,
        body.high-contrast textarea:focus,
        body.high-contrast .form-control:focus,
        body.high-contrast .form-select:focus,
        body.high-contrast .input-standard:focus {
            background-color: #ffff00 !important;
            color: #000000 !important;
            outline: none !important;
            font-weight: bold;
            box-shadow: 0 0 10px #ffff00;
        }

        body.high-contrast ::placeholder {
            color: rgba(255, 255, 0, 0.7) !important;
        }

        .skip-link {
            position: absolute;
            top: -100px;
            left: 0;
            background: var(--primary);
            color: white;
            padding: 15px;
            z-index: 2000;
            transition: top 0.3s;
            text-decoration: none;
            font-weight: bold;
        }
        .high-contrast .skip-link {
            color: #000f;
        }
        .skip-link:focus {
            top: 0;
        }

        :focus-visible {
            outline: 3px solid var(--primary) !important;
            outline-offset: 2px;
        }

        body.high-contrast :focus-visible {
            outline: 3px solid #ffffff !important;
        }
    </style>
    @stack('scripts')
</head>
<body id="main-body" class="{{ (isset($hideSidebar) && $hideSidebar) ? 'no-sidebar' : '' }}">
<a href="#main-content" class="skip-link">Przejdź do treści głównej</a>

    @unless(isset($hideSidebar) && $hideSidebar)
    <aside class="sidebar">
        <div class="brand">
            <a href="{{ route('home') }}"> VMControl </a>
        </div>
        
        <nav role="navigation" aria-label="Menu główne">
            @if(auth()->check() && auth()->user()->role === 'client')
            <div class="nav-section">Panel Klienta</div>
            <a href="{{ route('servers.index') }}" class="nav-link {{ request()->routeIs('servers.index') ? 'active' : '' }}">Moje Serwery</a>
            <a href="{{ route('finance.index') }}" class="nav-link {{ request()->routeIs('finance.*') ? 'active' : '' }}">Finanse</a>
            <a href="{{ route('support.index') }}" class="nav-link {{ request()->routeIs('support.*') ? 'active' : '' }}">Wsparcie</a>
            @endif

            @if(auth()->check() && auth()->user()->role === 'admin')
                <div class="nav-section">Administracja</div>
                <a href="{{ route('admin.nodes.index') }}" class="nav-link">Węzły (Nodes)</a>
                <a href="{{ route('admin.plans.index') }}" class="nav-link">Plany Hostingowe</a>
                <a href="{{ route('admin.systems.index') }}" class="nav-link">Systemy</a>
                <a href="{{ route('admin.servers.index') }}" class="nav-link">Wszystkie Serwery</a>
                <a href="{{ route('admin.logs.index') }}" class="nav-link">Logi Systemowe</a>
                <a href="{{ route('admin.support.index') }}" class="nav-link">Zarządzaj Zgłoszeniami</a>
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
    
    <main id="main-content" class="main-content" role="main">

        @unless(isset($hideNav) && $hideNav)
        <div class="top-bar" role="region" aria-label="Narzędzia dostępności">
            <button onclick="toggleContrast()" class="btn-access" aria-label="Zmień kontrast">Kontrast</button>
            <button onclick="resizeText(1)" class="btn-access" aria-label="Powiększ tekst">A+</button>
            <button onclick="resetText()" class="btn-access" aria-label="Rozmiar domyślny">A</button>
            <button onclick="resizeText(-1)" class="btn-access" aria-label="Pomniejsz tekst">A-</button>
        </div>
        @endunless
       
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
        let currentFontSize = parseInt(localStorage.getItem('fontSize')) || 100;

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Skrypt dostępności załadowany. Aktualny rozmiar:', currentFontSize);
            
            if (currentFontSize !== 100) {
                document.documentElement.style.fontSize = currentFontSize + '%';
            }

            if (localStorage.getItem('contrast') === 'high') {
                document.body.classList.add('high-contrast');
            }
        });

        function resizeText(multiplier) {
            currentFontSize += (multiplier * 10);

            if (currentFontSize < 60) currentFontSize = 60;
            if (currentFontSize > 200) currentFontSize = 200;

            document.documentElement.style.fontSize = currentFontSize + '%';
            
            localStorage.setItem('fontSize', currentFontSize);
            console.log('Zmieniono rozmiar na:', currentFontSize + '%');
        }

        function resetText() {
            currentFontSize = 100;
            
            document.documentElement.style.fontSize = '100%';
            
            localStorage.setItem('fontSize', 100);
            console.log('Zresetowano rozmiar do 100%');
        }

        function toggleContrast() {
            document.body.classList.toggle('high-contrast');
            
            if (document.body.classList.contains('high-contrast')) {
                localStorage.setItem('contrast', 'high');
                console.log('Włączono wysoki kontrast');
            } else {
                localStorage.removeItem('contrast');
                console.log('Wyłączono wysoki kontrast');
            }
        }
    </script>
</body>
</html>