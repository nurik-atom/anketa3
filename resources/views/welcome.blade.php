<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Divergents - Learning Management System</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <!-- Styles -->
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', sans-serif;
                height: 100vh;
                overflow: hidden;
                background: linear-gradient(135deg, #1e3c72 0%, #2a5298 25%, #234088 50%, #2f60da 75%, #3366ff 100%);
                background-size: 400% 400%;
                animation: gradientShift 15s ease infinite;
                position: relative;
            }

            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            .stars {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
            }

            .star {
                position: absolute;
                width: 2px;
                height: 2px;
                background: white;
                border-radius: 50%;
                animation: twinkle 3s infinite ease-in-out;
            }

            @keyframes twinkle {
                0%, 100% { opacity: 0; transform: scale(0); }
                50% { opacity: 1; transform: scale(1); }
            }

            .container {
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                position: relative;
                z-index: 10;
            }

            .main-card {
                background: rgba(255, 255, 255, 0.08);
                backdrop-filter: blur(25px);
                border: 1px solid rgba(255, 255, 255, 0.15);
                border-radius: 28px;
                padding: 0;
                max-width: 1100px;
                width: 100%;
                display: grid;
                grid-template-columns: 1fr 1fr;
                min-height: 650px;
                box-shadow: 0 30px 60px rgba(35, 64, 136, 0.3);
                animation: cardFloat 6s ease-in-out infinite;
                position: relative;
                overflow: hidden;
            }

            @keyframes cardFloat {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-12px); }
            }

            .left-panel {
                padding: 70px 60px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                position: relative;
                background: rgba(255, 255, 255, 0.03);
            }

            .right-panel {
                background: linear-gradient(135deg, rgba(35, 64, 136, 0.85) 0%, rgba(47, 96, 218, 0.85) 100%);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 70px 60px;
                position: relative;
                color: white;
            }

            .brand-logo {
                width: 280px;
                height: auto;
                margin-bottom: 50px;
                animation: logoFloat 4s ease-in-out infinite;
                filter: drop-shadow(0 12px 24px rgba(0, 0, 0, 0.4));
            }

            @keyframes logoFloat {
                0%, 100% { transform: translateY(0px) scale(1); }
                50% { transform: translateY(-12px) scale(1.03); }
            }

            .brand-description {
                font-size: 1.1rem;
                opacity: 0.85;
                margin-bottom: 60px;
                line-height: 1.7;
                color: #dbeafe;
                text-align: center;
                max-width: 400px;
            }

            .features-list {
                text-align: left;
                width: 100%;
                max-width: 320px;
            }

            .feature-item {
                display: flex;
                align-items: center;
                margin-bottom: 18px;
                font-size: 0.95rem;
                opacity: 0.9;
                color: #dbeafe;
                transition: all 0.3s ease;
            }

            .feature-item:hover {
                opacity: 1;
                transform: translateX(5px);
            }

            .feature-icon {
                width: 24px;
                height: 24px;
                background: rgba(255, 255, 255, 0.15);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 15px;
                font-size: 12px;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .login-section h1 {
                font-size: 2.5rem;
                font-weight: 700;
                color: white;
                margin-bottom: 15px;
                text-shadow: 0 2px 8px rgba(35, 64, 136, 0.3);
            }

            .login-section p {
                color: rgba(255, 255, 255, 0.85);
                font-size: 1.1rem;
                margin-bottom: 50px;
                line-height: 1.6;
            }

            .form-group {
                margin-bottom: 28px;
                position: relative;
            }

            .form-input {
                width: 100%;
                padding: 20px 24px;
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.15);
                border-radius: 16px;
                color: white;
                font-size: 1.05rem;
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
            }

            .form-input::placeholder {
                color: rgba(255, 255, 255, 0.6);
            }

            .form-input:focus {
                outline: none;
                border-color: rgba(47, 96, 218, 0.6);
                background: rgba(255, 255, 255, 0.12);
                box-shadow: 0 0 25px rgba(47, 96, 218, 0.2);
                transform: translateY(-3px);
            }

            .btn-login {
                width: 100%;
                padding: 20px 24px;
                background: linear-gradient(135deg, #234088 0%, #2f60da 50%, #3366ff 100%);
                border: none;
                border-radius: 16px;
                color: white;
                font-size: 1.05rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 12px 35px rgba(35, 64, 136, 0.4);
                position: relative;
                overflow: hidden;
            }

            .btn-login:hover {
                transform: translateY(-3px);
                box-shadow: 0 18px 45px rgba(35, 64, 136, 0.5);
                background: linear-gradient(135deg, #1e3c72 0%, #234088 50%, #2f60da 100%);
            }

            .btn-login:active {
                transform: translateY(-1px);
            }

            .btn-login::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: left 0.5s;
            }

            .btn-login:hover::before {
                left: 100%;
            }

            .login-footer {
                text-align: center;
                margin-top: 35px;
                color: rgba(255, 255, 255, 0.75);
                font-size: 1rem;
            }

            .login-footer a {
                color: #60a5fa;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s ease;
            }

            .login-footer a:hover {
                color: #93c5fd;
            }

            .success-message {
                background: rgba(34, 197, 94, 0.15);
                border: 1px solid rgba(34, 197, 94, 0.3);
                color: #4ade80;
                padding: 18px 24px;
                border-radius: 16px;
                margin-bottom: 25px;
                text-align: center;
                font-weight: 500;
                backdrop-filter: blur(10px);
            }

            .btn-dashboard {
                background: linear-gradient(135deg, #059669 0%, #10b981 100%);
                text-decoration: none;
                display: block;
                text-align: center;
            }

            .btn-dashboard:hover {
                background: linear-gradient(135deg, #047857 0%, #059669 100%);
            }

            .error-message {
                color: #f87171;
                font-size: 0.9rem;
                margin-top: 10px;
                font-weight: 500;
            }

            .floating-shapes {
                position: absolute;
                width: 100%;
                height: 100%;
                overflow: hidden;
                pointer-events: none;
            }

            .shape {
                position: absolute;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 50%;
                animation: float 25s infinite linear;
            }

            .shape:nth-child(1) {
                width: 100px;
                height: 100px;
                left: 5%;
                animation-delay: 0s;
            }

            .shape:nth-child(2) {
                width: 150px;
                height: 150px;
                left: 85%;
                animation-delay: 8s;
            }

            .shape:nth-child(3) {
                width: 80px;
                height: 80px;
                left: 65%;
                animation-delay: 16s;
            }

            @keyframes float {
                0% {
                    transform: translateY(100vh) rotate(0deg);
                    opacity: 0;
                }
                10% {
                    opacity: 1;
                }
                90% {
                    opacity: 1;
                }
                100% {
                    transform: translateY(-120px) rotate(360deg);
                    opacity: 0;
                }
            }

            /* Дополнительные декоративные элементы */
            .light-rays {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: radial-gradient(circle at 30% 20%, rgba(47, 96, 218, 0.1) 0%, transparent 50%);
                pointer-events: none;
            }

            @media (max-width: 768px) {
                body {
                    height: auto;
                    min-height: 100vh;
                    overflow: auto;
                }

                .container {
                    height: auto;
                    min-height: 100vh;
                    padding: 20px 10px;
                }

                .main-card {
                    grid-template-columns: 1fr;
                    max-width: 420px;
                    min-height: auto;
                    margin: 10px;
                    box-shadow: 0 20px 40px rgba(35, 64, 136, 0.3);
                }

                .right-panel {
                    order: 1;
                    padding: 30px 25px;
                    min-height: auto;
                    border-top: 1px solid rgba(255, 255, 255, 0.1);
                    margin-top: 20px;
                }

                .left-panel {
                    order: 0;
                    padding: 30px 25px;
                    min-height: auto;
                }

                .login-section h1 {
                    font-size: 1.8rem;
                    margin-bottom: 10px;
                }

                .login-section p {
                    font-size: 1rem;
                    margin-bottom: 30px;
                }

                .brand-logo {
                    width: 120px;
                    margin-bottom: 20px;
                }

                .brand-description {
                    font-size: 0.9rem;
                    margin-bottom: 25px;
                    line-height: 1.4;
                }

                .features-list {
                    max-width: 280px;
                }

                .feature-item {
                    font-size: 0.85rem;
                    margin-bottom: 12px;
                }

                .feature-icon {
                    width: 20px;
                    height: 20px;
                    font-size: 10px;
                    margin-right: 12px;
                }

                .form-input {
                    padding: 18px 20px;
                    font-size: 1rem;
                    border-radius: 14px;
                }

                .btn-login {
                    padding: 18px 20px;
                    font-size: 1rem;
                    border-radius: 14px;
                    margin-bottom: 20px;
                }

                .form-group {
                    margin-bottom: 20px;
                }

                .login-footer {
                    margin-top: 20px;
                    font-size: 0.9rem;
                }
            }
        </style>
    </head>
    <body>
        <!-- Animated Background Stars -->
        <div class="stars"></div>

        <!-- Light Rays Effect -->
        <div class="light-rays"></div>

        <!-- Floating Shapes -->
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>

        <div class="container">
            <div class="main-card">
                <!-- Left Panel - Login Form -->
                <div class="left-panel">
                    <div class="login-section">
                        <h1>Добро пожаловать</h1>
                        <p>Войдите в систему управления обучением Divergents</p>

                        @if (Route::has('login'))
                            @auth
                                <!-- User is authenticated -->
                                <div class="success-message">
                                    Привет, {{ Auth::user()->name }}! 👋
                                </div>
                                <a href="{{ url('/dashboard') }}" class="btn-login btn-dashboard">
                                    Перейти в панель управления
                                </a>
                            @else
                                <!-- Login Form -->
                                <form method="POST" action="{{ route('login') }}">
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
                                        <input
                                            type="password"
                                            id="password"
                                            name="password"
                                            class="form-input"
                                            placeholder="Пароль"
                                            required
                                        >
                                        @error('password')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn-login">
                                        Войти в систему
                                    </button>
                                </form>

                                <div class="login-footer">
                                    @if (Route::has('register'))
                                        Нет аккаунта? <a href="{{ route('register') }}">Создать аккаунт</a>
                                    @endif
                                </div>
                            @endauth
                        @endif
                    </div>
                </div>

                <!-- Right Panel - Branding -->
                <div class="right-panel">
                    <img src="{{ asset('logo.svg') }}" alt="Divergents Logo" class="brand-logo">
                    <p class="brand-description">
                        Инновационная платформа для управления обучением и комплексной оценки персонала с использованием современных методик
                    </p>

                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-icon">📋</div>
                            <span>Создание и управление анкетами</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">🧠</div>
                            <span>Психологические тесты и оценки</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">📊</div>
                            <span>Аналитика и детальные отчеты</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">👥</div>
                            <span>Управление кандидатами</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">⚡</div>
                            <span>Быстрая обработка результатов</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">🎯</div>
                            <span>Персонализированный подход</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Create animated stars
            function createStars() {
                const starsContainer = document.querySelector('.stars');
                const numberOfStars = 120;

                for (let i = 0; i < numberOfStars; i++) {
                    const star = document.createElement('div');
                    star.className = 'star';
                    star.style.left = Math.random() * 100 + '%';
                    star.style.top = Math.random() * 100 + '%';
                    star.style.animationDelay = Math.random() * 3 + 's';
                    starsContainer.appendChild(star);
                }
            }

            // Form interactions
            document.addEventListener('DOMContentLoaded', function() {
                createStars();

                // Add glow effect to inputs on focus
                const inputs = document.querySelectorAll('.form-input');
                inputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.parentElement.style.transform = 'scale(1.02)';
                    });

                    input.addEventListener('blur', function() {
                        this.parentElement.style.transform = 'scale(1)';
                    });
                });

                // Add parallax effect to logo
                const logo = document.querySelector('.brand-logo');
                let mouseX = 0, mouseY = 0;

                document.addEventListener('mousemove', function(e) {
                    mouseX = (e.clientX / window.innerWidth) * 100;
                    mouseY = (e.clientY / window.innerHeight) * 100;

                    if (logo) {
                        logo.style.transform = `translate(${mouseX * 0.02}px, ${mouseY * 0.02}px) scale(1.02)`;
                    }
                });
            });
        </script>
    </body>
</html>
