@extends('layouts.auth')

@section('title', 'Divergents - Learning Management System')

@section('content')
<h1>Добро пожаловать</h1>
<p>Войдите в систему управления обучением Divergents</p>

@if (session('status'))
    <div class="success-message">
        {{ session('status') }}
    </div>
@endif

@if (Route::has('login'))
    @auth
        <!-- User is authenticated -->
        <div class="success-message">
            Привет, {{ Auth::user()->name }}! 👋
        </div>
        <a href="{{ url('/dashboard') }}" class="btn-auth btn-dashboard">
            Перейти в панель управления
        </a>
    @else
        <!-- Login Form -->
        <form method="POST" action="/login">
            @csrf
            <div class="form-group">
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    placeholder="Email адрес"
                    value="{{ old('email') }}"
                    required
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div class="password-input-container">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input password-input"
                        placeholder="Пароль"
                        required
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
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-auth">
                Войти в систему
            </button>
        </form>

        <div class="auth-footer">
            @if (Route::has('password.request'))
                <div class="forgot-password-link">
                    <a href="{{ route('password.request') }}">Забыли пароль?</a>
                </div>
            @endif
            @if (Route::has('register'))
                Нет аккаунта? <a href="{{ route('register') }}">Создать аккаунт</a>
            @endif
        </div>
    @endauth
@endif

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