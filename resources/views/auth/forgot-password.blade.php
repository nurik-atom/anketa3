@extends('layouts.auth')

@section('title', 'Восстановление пароля - Divergents')

@section('content')
<h1>Восстановление пароля</h1>
<p>Введите ваш email адрес, и мы отправим вам ссылку для сброса пароля</p>

@session('status')
    <div class="success-message">
        {{ $value }}
    </div>
    
    <div class="spam-notice">
        <p><strong>Не нашли письмо?</strong> Проверьте папку "СПАМ" или "Нежелательная почта" - иногда письма попадают туда автоматически.</p>
    </div>
@endsession

<!-- Validation Errors -->
@if ($errors->any())
    <div class="error-message">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<!-- Forgot Password Form -->
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    
    <div class="form-group">
        <input
            type="email"
            id="email"
            name="email"
            class="form-input @if(session('status')) disabled-input @endif"
            placeholder="Email адрес"
            value="{{ old('email') }}"
            required
            @if(session('status')) disabled @endif
            @if(!session('status')) autofocus @endif
            autocomplete="username"
        >
    </div>

    @if(!session('status'))
    <button type="submit" class="btn-auth">
        Отправить ссылку для сброса пароля
    </button>
    @else
    <div class="resend-section">
        <p class="resend-text">Хотите отправить ссылку повторно?</p>
        <button type="submit" class="btn-auth btn-secondary">
            Отправить повторно
        </button>
    </div>
    @endif
</form>

<div class="auth-footer">
    <a href="{{ route('login') }}">← Вернуться к входу</a>
</div>
@endsection
