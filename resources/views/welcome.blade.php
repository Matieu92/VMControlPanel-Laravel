@extends('layouts.app', ['hideSidebar' => true])

@section('content')
<div style="max-width: 900px; margin: 0 auto; text-align: center; padding-top: 60px;">
    
    <h1 style="font-size: 3rem; margin-bottom: 20px; color: var(--text-main);">
        VM Control Manager
    </h1>
    
    <p style="font-size: 1.2rem; color: var(--text-muted); margin-bottom: 40px;">
        Zaawansowany system zarządzania infrastrukturą serwerową.
        Dostępność, wydajność i kontrola w jednym miejscu.
    </p>

    <div style="background: var(--bg-card); padding: 40px; border: 1px solid var(--border-color); border-radius: 8px;">
        <h2 style="margin-top: 0;">Panel Logowania</h2>
        <p style="margin-bottom: 30px;">Zaloguj się, aby uzyskać dostęp do swoich zasobów.</p>

        <div style="display: flex; justify-content: center; gap: 15px;">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/') }}" class="btn btn-primary">Wróć do Panelu</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Zaloguj się</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn" style="border: 1px solid var(--border-color);">Rejestracja</a>
                    @endif
                @endauth
            @endif
        </div>
    </div>

    <div style="margin-top: 50px; text-align: left; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
        <div>
            <h3 style="color: var(--primary);">WCAG 2.1</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted);">Pełne wsparcie dla czytników ekranu i tryb wysokiego kontrastu.</p>
        </div>
        <div>
            <h3 style="color: var(--primary);">Skalowalność</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted);">Inteligentny przydział zasobów na węzłach fizycznych.</p>
        </div>
        <div>
            <h3 style="color: var(--primary);">Bezpieczeństwo</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted);">Izolowane środowiska wirtualne i szyfrowane połączenia.</p>
        </div>
    </div>
</div>
@endsection