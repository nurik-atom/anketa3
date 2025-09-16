@extends('layouts.auth')

@section('title', 'Восстановление пароля - Divergents')

@section('content')
<h1>Восстановление пароля</h1>
<p>Введите ваш email адрес, и мы отправим вам ссылку для сброса пароля</p>

@session('status')
    <div class="success-message">
        {{ $value }}
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
            class="form-input"
            placeholder="Email адрес"
            value="{{ old('email') }}"
            required
            autofocus
            autocomplete="username"
        >
    </div>

    <button type="submit" class="btn-auth">
        Отправить ссылку для сброса пароля
    </button>
</form>

<div class="auth-footer">
    <a href="{{ route('login') }}">← Вернуться к входу</a>
</div>
@endsection
