@extends('layouts.auth')

@section('title', 'Сброс пароля - Divergents')

@section('content')
<h1>Сброс пароля</h1>
<p>Введите новый пароль для вашего аккаунта</p>

<!-- Validation Errors -->
@if ($errors->any())
    <div class="error-message">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<!-- Reset Password Form -->
<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="form-group">
        <input
            type="email"
            id="email"
            name="email"
            class="form-input"
            placeholder="Email адрес"
            value="{{ old('email', $request->email) }}"
            required
            autofocus
            autocomplete="username"
        >
    </div>

    <div class="form-group">
        <div class="password-input-container">
            <input
                type="password"
                id="password"
                name="password"
                class="form-input password-input"
                placeholder="Новый пароль"
                required
                autocomplete="new-password"
            >
            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                <svg class="password-icon show-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <svg class="password-icon hide-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <path d="m1 1 22 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6.71 6.71C4.22 8.04 2.45 10.7 1 12c1.5 2.83 4.95 8 11 8 1.58 0 2.8-.2 3.9-.64" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10.5 10.5A2 2 0 0 1 14 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="form-group">
        <div class="password-input-container">
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-input password-input"
                placeholder="Подтвердите новый пароль"
                required
                autocomplete="new-password"
            >
            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                <svg class="password-icon show-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <svg class="password-icon hide-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <path d="m1 1 22 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6.71 6.71C4.22 8.04 2.45 10.7 1 12c1.5 2.83 4.95 8 11 8 1.58 0 2.8-.2 3.9-.64" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10.5 10.5A2 2 0 0 1 14 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>

    <button type="submit" class="btn-auth">
        Сбросить пароль
    </button>
</form>

<div class="auth-footer">
    <a href="{{ route('login') }}">← Вернуться к входу</a>
</div>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const showIcon = button.querySelector('.show-icon');
    const hideIcon = button.querySelector('.hide-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        showIcon.style.display = 'none';
        hideIcon.style.display = 'block';
    } else {
        input.type = 'password';
        showIcon.style.display = 'block';
        hideIcon.style.display = 'none';
    }
}
</script>
@endsection
