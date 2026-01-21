@extends('layouts.app', ['hideSidebar' => true, 'hideNav' => false])

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
    <div class="card" style="width: 100%; max-width: 450px;">
        <div class="page-header" style="text-align: center; border-bottom: none;">
            <h1 class="page-title">Rejestracja</h1>
            <p class="page-subtitle">Utwórz konto w systemie VMControl</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div style="margin-bottom: 1.25rem;">
                <label for="name" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Pseudonim') }}</label>
                <input id="name" class="input-standard" style="width: 100%;" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
                @error('name')
                    <span role="alert" style="color: var(--danger); font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label for="email" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Email') }}</label>
                <input id="email" class="input-standard" style="width: 100%;" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
                @error('email')
                    <span role="alert" style="color: var(--danger); font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label for="password" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Hasło') }}</label>
                <input id="password" class="input-standard" style="width: 100%;" type="password" name="password" required autocomplete="new-password" />
                @error('password')
                    <span role="alert" style="color: var(--danger); font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="password_confirmation" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Potwierdź hasło') }}</label>
                <input id="password_confirmation" class="input-standard" style="width: 100%;" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1rem;">
                    {{ __('Zarejestruj') }}
                </button>
                <div style="text-align: center;">
                    <a href="{{ route('login') }}" style="color: var(--primary); font-size: 0.85rem; text-decoration: none;">
                        {{ __('Masz już konto?') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection