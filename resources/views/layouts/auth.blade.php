<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Divergents - Learning Management System')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/auth-pages.css') }}" rel="stylesheet">

        @stack('styles')
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
                <!-- Left Panel - Auth Form -->
                <div class="left-panel">
                    <div class="auth-section">
                        @yield('content')
                    </div>
                </div>

                <!-- Right Panel - Branding -->
                <div class="right-panel">
                    <div class="logos-container">
                        <img src="{{ asset('logos/divergents_logo.png') }}" alt="Divergents Logo" class="brand-logo brand-logo-left">
                        <img src="{{ asset('logos/talents_lab_logo.png') }}" alt="Talents Lab Logo" class="brand-logo brand-logo-right">
                    </div>
                    <p class="brand-description">
{{--                        Добро пожаловать!<br>--}}
                        Войдите на онлайн-портал Talents Lab — современную платформу с базой резюме
                    </p>

                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-icon">📋</div>
                            <span>Создание и управление анкетами</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">🧠</div>
                            <span>Психометрические тесты для оценки потенциала кандидатов</span>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">🎯</div>
                            <span>Индивидуальный подбор вакансий на основе способностей</span>
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

        @stack('scripts')
    </body>
</html>
