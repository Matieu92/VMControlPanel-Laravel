@extends('layouts.app')

@section('content')
@push('styles')
<style>
    .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(4px);
    }

    .payment-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 30px;
        border-radius: 12px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
    }

    .mock-card-input {
        background: #1a1a1a;
        border: 1px solid #333;
        padding: 15px;
        border-radius: 8px;
        margin-top: 10px;
    }

    .spinner-large {
        width: 50px;
        height: 50px;
        border: 3px solid var(--primary);
        border-bottom-color: transparent;
        border-radius: 50%;
        display: inline-block;
        animation: rotation 0.8s linear infinite;
    }

    @keyframes rotation {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush
<div class="page-header">
    <h1 class="page-title">Finanse i Portfel</h1>
    <p class="page-subtitle">Zarządzaj swoimi środkami i przeglądaj historię transakcji.</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
    <div class="card" style="text-align: center; padding: 40px;" role="region" aria-label="Twój portfel">
        <span style="font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase;" id="balance-label">Dostępne środki</span>
        <div style="font-size: 3rem; font-weight: 800; margin: 10px 0;" aria-describedby="balance-label" aria-live="polite">
            {{ number_format(Auth::user()->balance, 2) }} <span style="font-size: 1.5rem;">PLN</span>
        </div>
        
        <form action="{{ route('finance.deposit') }}" method="POST" style="margin-top: 20px;">
            @csrf
            <label for="amountinput" class="sr-only">Kwota doładowania w PLN</label>
            <input type="number" name="amount" id="amountinput" value="50" min="10" class="form-control" style="margin-bottom: 10px; text-align: center;" aria-describedby="amount-hint">
            <span id="amount-hint" class="sr-only">Minimalna kwota doładowania to 10 PLN.</span>
            <button type="button" class="btn btn-primary" style="width: 100%;" onclick="openPaymentModal()" aria-haspopup="dialog" aria-controls="payment-modal">
                Doładuj portfel
            </button>

            <div id="payment-modal" class="modal-overlay" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="modal-title">
                <div class="payment-card">
                    <div id="payment-step-1">
                        <span class="h3" id="modal-title">Bramka Płatności</span>
                        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px;">Wybrano doładowanie kwotą: <strong id="display-amount" aria-live="polite">0</strong> PLN</p>
                        
                        <div class="mock-card-input" role="group" aria-label="Dane karty płatniczej">
                            <div style="font-size: 0.7rem; text-transform: uppercase; color: var(--text-muted);" id="card-label">Numer karty</div>
                            <div style="letter-spacing: 2px;" aria-describedby="card-label">**** **** **** 4421</div>
                        </div>

                        <button type="button" class="btn btn-primary" style="width: 100%; margin-top: 20px;" onclick="processPayment()" aria-label="Potwierdź płatność i przelej środki">
                            Zapłać i potwierdź
                        </button>
                        <button type="button" class="btn" style="width: 100%; margin-top: 10px; border: none;" onclick="closePaymentModal()" aria-label="Anuluj">
                            Anuluj
                        </button>
                    </div>

                    <div id="payment-step-2" style="display: none; text-align: center; padding: 20px 0;" role="status" aria-live="assertive">
                        <div class="spinner-large" aria-hidden="true"></div>
                        <p style="margin-top: 20px; font-weight: bold;">Przetwarzanie transakcji...</p>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Proszę nie odświeżać strony.</p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card" role="region" aria-label="Historia transakcji">
        <span class="h3" id="history-title">Ostatnie operacje</span>
        <table class="data-table" aria-labelledby="history-title">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Opis</th>
                    <th>Kwota</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $t)
                <tr>
                    <td>{{ $t->created_at->format('d.m.Y H:i') }}</td>
                    <td>{{ $t->description }}</td>
                    <td style="font-weight: bold; color: {{ $t->type == 'deposit' ? 'var(--success)' : 'var(--danger)' }}">
                        {{ $t->type == 'deposit' ? '+' : '-' }}{{ number_format($t->amount, 2) }} PLN
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top: 15px;">{{ $transactions->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openPaymentModal() {
    const amount = document.querySelector('input[name="amount"]').value;
    document.getElementById('display-amount').innerText = amount;
    document.getElementById('payment-modal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('payment-modal').style.display = 'none';
}

function processPayment() {
    document.getElementById('payment-step-1').style.display = 'none';
    document.getElementById('payment-step-2').style.display = 'block';

    setTimeout(() => {
        document.querySelector('form[action$="deposit"]').submit();
    }, 2000);
}
</script>
@endpush