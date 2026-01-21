@extends('layouts.app', ['hideSidebar' => true, 'hideNav' => true])

@push('styles')
<style>
    .landing-content { padding-top: 4.5rem; }
    
    .section-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .landing-header {
        position: fixed;
        top: 0; left: 0; right: 0;
        height: 4.5rem;
        background: var(--bg-card);
        border-top: 4px solid var(--primary);
        border-bottom: none;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .header-inner {
        max-width: 1200px;
        margin: 0 auto;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
    }

    .brand-logo {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-main);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-wcag-tools {
        display: flex;
        gap: 4px;
        background: var(--bg-body);
        padding: 4px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
    }

    .btn-access {
        background: transparent;
        border: 1px solid transparent;
        color: var(--text-main);
        padding: 4px 12px;
        font-size: 0.75rem;
        font-weight: 800;
        cursor: pointer;
    }

    .page-section { padding: 100px 0; }
    .bg-alt { background: var(--bg-card); }
    .bg-main { background: var(--bg-body); }
    
    .section-divider {
        height: 1px;
        width: 100%;
        background: linear-gradient(90deg, transparent, var(--border-color), transparent);
    }

    .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2.5rem; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: center; }

    .feature-card, .plan-card {
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        padding: 2.5rem;
        border-radius: 12px;
        transition: 0.3s ease;
    }
    .bg-alt .feature-card, .bg-alt .plan-card { background: var(--bg-card); }

    .plan-card.featured { border: 2px solid var(--primary); position: relative; }
    .plan-badge {
        position: absolute;
        top: -14px; left: 50%; transform: translateX(-50%);
        background: var(--primary); color: #fff; padding: 4px 16px;
        border-radius: 20px; font-size: 0.7rem; font-weight: 900; text-transform: uppercase;
    }

    .high-contrast .plan-badge {
        color: #000f;
    }

    .console-box { background: #0d1117; border-radius: 10px; border: 1px solid #30363d; overflow: hidden; }
    .latency-card { background: var(--bg-body); padding: 2rem; border: 1px solid var(--border-color); border-radius: 8px; }

    .footer-minimal {
        padding: 4rem 0;
        background: var(--bg-card);
        border-top: 1px solid var(--border-color);
        color: var(--text-muted);
    }

    @media (max-width: 1024px) { .grid-3, .grid-2 { grid-template-columns: 1fr; } }
    .high-contrast .plan-card, .high-contrast .feature-card, .high-contrast .latency-card {
        border: 2px solid #FFFF00 !important; background: #000 !important;
    }

    .hero-premium {
    position: relative;
    background: linear-gradient(rgba(2, 0, 36, 0.65), rgba(9, 9, 121, 0.45)),
                    url('/images/hero-bg.jpg');
    border-top: none;
    padding-top: 14rem;
    }

    .hero-premium h1, .hero-premium p {
        color: #ffffff !important;
    }

    .high-contrast .hero-premium {
        background: #000000 !important;
    }

    .top-bar {
        display: none;
    }
</style>
@endpush

@section('content')
<header class="landing-header">
    <div class="header-inner">
        <a href="/" class="brand-logo">
            <span style="width: 10px; height: 26px; background: var(--primary); display: inline-block;"></span>
            VM-CONTROL
        </a>

        <div class="header-wcag-tools" role="region" aria-label="Narzędzia ułatwień dostępu">
            <button onclick="toggleContrast()" class="btn-access" aria-label="Włącz lub wyłącz tryb wysokiego kontrastu">Kontrast</button>
            <button onclick="resizeText(1)" class="btn-access" aria-label="Zwiększ rozmiar tekstu">A+</button>
            <button onclick="resetText()" class="btn-access" aria-label="Przywróć domyślny rozmiar tekstu">A</button>
            <button onclick="resizeText(-1)" class="btn-access" aria-label="Zmniejsz rozmiar tekstu">A-</button>
        </div>

        <div class="header-actions">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary">Panel Sterowania</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-access" style="color: var(--text-main); font-weight: 800; margin-right: 1.5rem;">Logowanie</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Rozpocznij</a>
            @endauth
        </div>
    </div>
</header>

<div class="landing-content" id="main-content">
    <section class="page-section hero-premium" role="img" aria-label="Sekcja powitalna: Nowoczesna infrastruktura serwerowa">
        <div class="section-container" style="text-align: center;">
            <h1 style="font-size: clamp(2.5rem, 6vw, 4.8rem); font-weight: 900; line-height: 1.1; margin-bottom: 2rem;">
                Potęga chmury pod Twoją kontrolą
            </h1>
            <p style="font-size: 1.3rem; color: var(--text-muted); max-width: 750px; margin: 0 auto 3.5rem auto; line-height: 1.6;">
                Profesjonalna infrastruktura wirtualna w standardzie Tier III. Pełna izolacja zasobów i wydajność dla wymagających projektów.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 1.25rem 3.5rem;" aria-label="Wdróż nowy serwer wirtualny - rejestracja">Wdróż Serwer</a>
                <a href="#oferta" class="btn btn-access" style="border: 1px solid var(--border-color); padding: 1.25rem 3.5rem;" aria-label="Przejdź do sekcji cennika planów hostingowych">Cennik Usług</a>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <section class="page-section bg-alt">
        <div class="section-container">
            <div class="grid-3">
                <article class="feature-card">
                    <h2 class="h3" style="border-left: 4px solid var(--primary); padding-left: 1.25rem; margin-bottom: 1.5rem;">NVMe RAID 10</h2>
                    <p class="text-muted" style="font-size: 0.95rem; line-height: 1.6;">Dane są bezpieczne i błyskawicznie dostępne dzięki nowoczesnym macierzom o niskim czasie dostępu.</p>
                </article>
                <article class="feature-card">
                    <h2 class="h3" style="border-left: 4px solid var(--primary); padding-left: 1.25rem; margin-bottom: 1.5rem;">Ochrona L3-L7</h2>
                    <p class="text-muted" style="font-size: 0.95rem; line-height: 1.6;">Zaawansowane systemy inspekcji pakietów chronią aplikacje przed atakami DDoS i nadużyciami.</p>
                </article>
                <article class="feature-card">
                    <h2 class="h3" style="border-left: 4px solid var(--primary); padding-left: 1.25rem; margin-bottom: 1.5rem;">Izolacja KVM</h2>
                    <p class="text-muted" style="font-size: 0.95rem; line-height: 1.6;">Pełna wirtualizacja sprzętowa gwarantuje, że zasoby RAM i CPU należą wyłącznie do Ciebie.</p>
                </article>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <section class="page-section bg-main" aria-label="Szczegóły techniczne i terminal">
        <div class="section-container">
            <div class="grid-2">
                <div>
                    <h2 style="font-size: 2.8rem; font-weight: 900; margin-bottom: 2rem;">Precyzyjna alokacja</h2>
                    <p class="text-muted" style="font-size: 1.15rem; line-height: 1.7; margin-bottom: 2.5rem;">
                        System orkiestracji dba o dostępność Twoich zasobów. Każda instancja posiada gwarantowaną moc obliczeniową bez zjawiska oversellingu.
                    </p>
                    <ul style="list-style: none; padding: 0; display: grid; gap: 1.25rem;">
                        <li style="display: flex; align-items: center; gap: 12px; font-weight: 700;">
                            <span style="width: 10px; height: 10px; background: var(--primary); border-radius: 50%;"></span>
                            API First: Pełna automatyzacja procesów
                        </li>
                        <li style="display: flex; align-items: center; gap: 12px; font-weight: 700;">
                            <span style="width: 10px; height: 10px; background: var(--primary); border-radius: 50%;"></span>
                            Standard Tier III: Gwarancja ciągłości pracy
                        </li>
                    </ul>
                </div>
                <div class="console-box" role="region" aria-label="Podgląd terminala systemowego maszyny wirtualnej">
                    <div style="background: #161b22; padding: 12px 18px; display: flex; gap: 8px; border-bottom: 1px solid #30363d;" aria-hidden="true">
                        <span style="width: 10px; height: 10px; background: #ff5f56; border-radius: 50%;"></span>
                        <span style="width: 10px; height: 10px; background: #27c93f; border-radius: 50%;"></span>
                    </div>
                    <div style="padding: 1.8rem; font-family: monospace; font-size: 0.95rem; color: #c9d1d9;">
                        <span style="color: #7ee787;">admin@vm-control:~$</span> vm info --id 771<br>
                        Status: <span style="color: #a5d6ff;">Running</span><br>
                        IPv4: 185.255.xx.xx<br>
                        Network: 1.2 Gbps current<br>
                        <span style="border-right: 2px solid var(--primary); padding-right: 4px;"></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <section id="oferta" class="page-section bg-alt">
        <div class="section-container">
            <header style="text-align: center; margin-bottom: 5rem;">
                <h2 style="font-size: 2.5rem; font-weight: 900;">Wybierz swój plan</h2>
                <p class="text-muted">Proste zasady, bez ukrytych kosztów.</p>
            </header>
            <div class="grid-3">
                <article class="plan-card" aria-labelledby="plan-starter-title">
                    <h3 class="h4">Cloud Starter</h3>
                    <div class="price-value">19.99 <span class="price-unit">PLN / msc</span></div>
                    <ul style="list-style: none; padding: 0; margin-bottom: 3rem; flex-grow: 1;">
                        <li style="padding: 0.8rem 0; border-bottom: 1px solid var(--border-color);">2 vCore CPU</li>
                        <li style="padding: 0.8rem 0; border-bottom: 1px solid var(--border-color);">4 GB RAM DDR4</li>
                        <li style="padding: 0.8rem 0; border-bottom: 1px solid var(--border-color);">40 GB NVMe SSD</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-access" style="border: 1px solid var(--border-color);" aria-label="Wybierz plan Cloud Starter za 19.99 PLN miesięcznie">Wybierz</a>
                </article>

                <article class="plan-card featured">
                    <span class="plan-badge">Najpopularniejszy</span>
                    <h3 class="h4">Cloud Pro</h3>
                    <div class="price-value">49.99 <span class="price-unit">PLN / msc</span></div>
                    <ul style="list-style: none; padding: 0; margin-bottom: 3rem; flex-grow: 1;">
                        <li style="padding: 0.8rem 0; border-bottom: 1px solid var(--border-color);">4 vCore CPU</li>
                        <li style="padding: 0.8rem 0; border-bottom: 1px solid var(--border-color);">8 GB RAM DDR4</li>
                        <li style="padding: 0.8rem 0; border-bottom: 1px solid var(--border-color);">80 GB NVMe SSD</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-primary" aria-label="Wybierz plan Cloud Pro za 49.99 PLN miesięcznie">Wybierz</a>
                </article>

                <article class="plan-card">
                    <h3 class="h4">Cloud Business</h3>
                    <div class="price-value">99.99 <span class="price-unit">PLN / msc</span></div>
                    <ul style="list-style: none; padding: 0; margin-bottom: 3rem; flex-grow: 1;">
                        <li style="padding: 0.8rem 0; border-bottom: 1px solid var(--border-color);">8 vCore CPU</li>
                        <li style="padding: 0.8rem 0; border-bottom: 1px solid var(--border-color);">16 GB RAM DDR4</li>
                        <li style="padding: 0.8rem 0; border-bottom: 1px solid var(--border-color);">160 GB NVMe SSD</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-access" style="border: 1px solid var(--border-color);" aria-label="Wybierz plan Cloud Business za 99.99 PLN miesięcznie">Wybierz</a>
                </article>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <section id="siec" class="page-section bg-main">
        <div class="section-container">
            <div class="grid-2">
                <div>
                    <h2 style="font-size: 2.5rem; font-weight: 900; margin-bottom: 1.5rem;">Niskie opóźnienia</h2>
                    <p class="text-muted" style="font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem;">Bezpośrednie połączenia z punktami IXP gwarantują błyskawiczną komunikację Twoich usług.</p>
                    <div class="latency-card" role="table" aria-label="Statystyki opóźnień sieciowych w lokalizacjach europejskich">
                        <div aria-label="Warszawa: 2 milisekundy" style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--border-color); font-family: monospace;">
                            <span>Warszawa (PL-IX)</span> <span style="color: var(--primary); font-weight: 900;">2ms</span>
                        </div>
                        <div aria-label="Frankfurt: 11 milisekund" style="display: flex; justify-content: space-between; padding: 0.75rem 0; font-family: monospace;">
                            <span>Frankfurt (DE-CIX)</span> <span style="color: var(--primary); font-weight: 900;">11ms</span>
                        </div>
                    </div>
                </div>
                <div aria-label="Przepustowość węzłów 10Gbps" style="text-align: center; border: 1px solid var(--border-color); padding: 5rem 2rem; border-radius: 12px; background: rgba(var(--primary-rgb), 0.02);">
                    <div style="font-size: 4rem; font-weight: 900; color: var(--primary); margin-bottom: 1rem;">10 Gbps</div>
                    <p style="font-weight: 800; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem;">Przepustowość węzła</p>
                </div>
            </div>
        </div>
    </section>

    <aside style="background: var(--bg-card); border-top: 1px solid var(--border-color); padding: 1.25rem 0;">
        <div class="section-container" style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 700; opacity: 0.7;">
            <span>STANDARD: WCAG 2.1 AA</span>
            <span>INFRASTRUKTURA: TIER III COMPLIANT</span>
            <span>KONTRAST: AAA RATIO</span>
        </div>
    </aside>

    <footer class="footer-minimal">
        <div class="section-container">
            <p style="font-weight: 900; margin-bottom: 1.25rem; font-size: 1.1rem; letter-spacing: 1px;">VM-CONTROL INFRASTRUCTURE</p>
            <p style="font-size: 0.85rem; opacity: 0.6;">&copy; 2026 Mateusz Brodzik. Wszystkie prawa zastrzeżone.</p>
        </div>
    </footer>
</div>
@endsection