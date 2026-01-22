@extends('layouts.app')

@push('styles')
<style>
    .category-radio {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .category-card {
        display: block;
        cursor: pointer;
    }

    .category-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.25rem 1rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        transition: all 0.2s ease;
        text-align: center;
        height: 100%;
    }

    .category-card:hover .category-content {
        border-color: var(--primary);
        transform: translateY(-2px);
    }

    .category-radio:checked + .category-content {
        border-color: var(--primary);
        background: rgba(var(--primary-rgb), 0.05);
        box-shadow: 0 0 0 2px var(--primary);
    }

    .category-title {
        display: block;
        font-weight: 700;
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
        color: var(--text-main);
    }

    .category-desc {
        display: block;
        font-size: 0.7rem;
        color: var(--text-muted);
        line-height: 1.3;
    }

    .form-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
    .form-group { display: flex; flex-direction: column; gap: 0.5rem; }
    .input-standard, .select-standard, .textarea-standard {
        width: 100%; padding: 0.75rem 1rem; background: var(--bg-body);
        border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-main);
    }
    .textarea-standard { min-height: 180px; resize: vertical; }

    @media (max-width: 1200px) { .category-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .category-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Nowe Zgłoszenie</h1>
    <p class="page-subtitle" id="category-label">Wybierz odpowiednią kategorię, aby przyspieszyć proces obsługi.</p>
</div>

<form action="{{ route('support.store') }}" method="POST">
    @csrf
    
    <div class="category-grid" role="radiogroup" aria-labelledby="category-label">
        <label class="category-card">
            <input type="radio" name="category" value="technical" class="category-radio" required checked>
            <span class="category-content">
                <span class="category-title">Problem Techniczny</span>
                <span class="category-desc">Błędy systemu, SSH, konfiguracja</span>
            </span>
        </label>

        <label class="category-card">
            <input type="radio" name="category" value="upgrade" class="category-radio">
            <span class="category-content">
                <span class="category-title">Zmiana Planu</span>
                <span class="category-desc">Zwiększenie zasobów, upgrade instancji</span>
            </span>
        </label>

        <label class="category-card">
            <input type="radio" name="category" value="emergency" class="category-radio">
            <span class="category-content">
                <span class="category-title">Awaria Krytyczna</span>
                <span class="category-desc">Brak łączności, serwer nie działa</span>
            </span>
        </label>

        <label class="category-card">
            <input type="radio" name="category" value="billing" class="category-radio">
            <span class="category-content">
                <span class="category-title">Płatności</span>
                <span class="category-desc">Problemy z portfelem i fakturami</span>
            </span>
        </label>

        <label class="category-card">
            <input type="radio" name="category" value="migration" class="category-radio">
            <span class="category-content">
                <span class="category-title">Migracja</span>
                <span class="category-desc">Przenoszenie danych między usługami</span>
            </span>
        </label>

        <label class="category-card">
            <input type="radio" name="category" value="security" class="category-radio">
            <span class="category-content">
                <span class="category-title">Bezpieczeństwo</span>
                <span class="category-desc">Zgłoszenie nadużyć lub włamań</span>
            </span>
        </label>

        <label class="category-card">
            <input type="radio" name="category" value="feature" class="category-radio">
            <span class="category-content">
                <span class="category-title">Sugestia Funkcji</span>
                <span class="category-desc">Pomysły na rozwój platformy</span>
            </span>
        </label>

        <label class="category-card">
            <input type="radio" name="category" value="other" class="category-radio">
            <span class="category-content">
                <span class="category-title">Inne Pytania</span>
                <span class="category-desc">Ogólne zapytania niezwiązane z powyższymi</span>
            </span>
        </label>
    </div>

    <div class="card" style="padding: 2rem;">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="subject" id="subject-label">Temat wiadomości</label>
                <input type="text" id="subject" name="subject" class="input-standard" placeholder="Wpisz krótki temat..." required aria-invalid="{{ $errors->has('subject') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('subject') ? 'error-subject' : 'subject-label' }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="server_id">Dotyczy serwera</label>
                <select name="server_id" id="server_id" class="select-standard" aria-label="Wybierz serwer, którego dotyczy problem">
                    <option value="">Nie dotyczy / Inne</option>
                    @foreach($servers as $server)
                        <option value="{{ $server->id }}">{{ $server->hostname }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="message">Szczegółowy opis</label>
            <textarea id="message" name="message" class="textarea-standard" placeholder="Opisz dokładnie swój problem..." required></textarea>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary" aria-label="Wyślij zgłoszenie do pomocy technicznej">Wyślij zgłoszenie</button>
            <a href="{{ route('support.index') }}" class="btn btn-access" style="border: 1px solid var(--border-color);" aria-label="Anuluj wysyłanie">Anuluj</a>
        </div>
    </div>
</form>
@endsection