@extends('layouts.app', ['hideSidebar' => true, 'hideNav' => false])

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <div class="page-header" style="text-align: center; border-bottom: none;">
            <h1 class="page-title">Logowanie</h1>
            <p class="page-subtitle">Zaloguj się do panelu VMControl</p>
        </div>

        @if (session('status'))
            <div role="alert" style="background-color: var(--success); color: white; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div style="margin-bottom: 1.5rem;">
                <label for="email" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Email') }}</label>
                <input id="email" class="input-standard" style="width: 100%;" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                @error('email')
                    <span role="alert" style="color: var(--danger); font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="password" class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Hasło') }}</label>
                <input id="password" class="input-standard" style="width: 100%;" type="password" name="password" required autocomplete="current-password" />
                @error('password')
                    <span role="alert" style="color: var(--danger); font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px;">
                <input id="remember_me" type="checkbox" name="remember" style="width: 18px; height: 18px; cursor: pointer;">
                <label for="remember_me" style="font-size: 0.9rem; color: var(--text-muted); cursor: pointer;">{{ __('Zapamiętaj mnie') }}</label>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1rem;">
                    {{ __('Zaloguj') }}
                </button>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="color: var(--primary); font-size: 0.85rem; text-decoration: none;">
                            {{ __('Nie pamiętasz hasła?') }}
                        </a>
                    @endif
                    <a href="{{ route('register') }}" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none;">Rejestracja</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection