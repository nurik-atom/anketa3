<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ваша анкета добавлена в TalentsLab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #2c3e50;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .header {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 50%, #60a5fa 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .logos-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
        }
        
        .logo {
            height: 60px;
            width: auto;
            /* Убираем filter для Gmail */
            transition: transform 0.3s ease;
        }
        
        .logo:hover {
            transform: scale(1.05);
        }
        
        .divergents-logo {
            height: 70px;
        }
        
        .talents-logo {
            height: 55px;
        }
        
        /* Fallback для Gmail - белый фон для логотипов */
        .logo-fallback {
            background: #ffffff;
            padding: 10px;
            border-radius: 8px;
            display: inline-block;
        }
        
        .title {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 2;
        }
        
        .content {
            padding: 50px 40px;
        }
        
        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .intro-text {
            font-size: 18px;
            color: #475569;
            text-align: center;
            margin-bottom: 40px;
            line-height: 1.7;
        }
        
        .email-highlight {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border: 2px solid #3b82f6;
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .email-highlight::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa, #3b82f6);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        .email-label {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 15px;
        }
        
        .email-value {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            font-weight: 700;
            color: #2563eb;
            background: #ffffff;
            padding: 15px 25px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        }
        
        .steps-container {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 20px;
            padding: 35px;
            margin: 40px 0;
            border: 1px solid #e2e8f0;
            position: relative;
        }
        
        .steps-title {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .steps-list {
            list-style: none;
            counter-reset: step-counter;
        }
        
        .steps-list li {
            counter-increment: step-counter;
            margin: 20px 0;
            padding: 20px 20px 20px 60px;
            background: #ffffff;
            border-radius: 12px;
            border-left: 4px solid #3b82f6;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            position: relative;
            transition: transform 0.2s ease;
        }
        
        .steps-list li:hover {
            transform: translateX(5px);
        }
        
        .steps-list li::before {
            content: counter(step-counter);
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: #3b82f6;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }
        
        /* Fallback для Gmail - обычная нумерация */
        .steps-list-fallback {
            list-style: decimal;
            padding-left: 20px;
        }
        
        .steps-list-fallback li {
            margin: 20px 0;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            border-left: 4px solid #3b82f6;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .cta-section {
            text-align: center;
            margin: 50px 0;
        }
        
        .register-button {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff;
            padding: 18px 45px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 18px;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .register-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .register-button:hover::before {
            left: 100%;
        }
        
        .register-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(37, 99, 235, 0.5);
        }
        
        .features-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 20px;
            padding: 35px;
            margin: 40px 0;
        }
        
        .features-title {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .features-list {
            list-style: none;
        }
        
        .features-list li {
            margin: 15px 0;
            padding: 15px 20px;
            background: #ffffff;
            border-radius: 10px;
            border-left: 4px solid #0ea5e9;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            position: relative;
        }
        
        .features-list li::before {
            content: '✓';
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            background: #0ea5e9;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
        }
        
        .footer {
            background: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer-text {
            color: #64748b;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .footer-note {
            color: #94a3b8;
            font-size: 14px;
            font-style: italic;
        }
        
        /* Gmail совместимость */
        .gmail-fix {
            min-width: 100%;
        }
        
        /* Убираем сложные анимации для Gmail */
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            .header::before {
                display: none;
            }
            .email-highlight::before {
                display: none;
            }
        }
        
        /* Адаптивные стили */
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .email-container {
                border-radius: 15px;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            .logos-container {
                gap: 20px;
                flex-direction: column;
            }
            
            .logo {
                height: 50px;
            }
            
            .divergents-logo {
                height: 60px;
            }
            
            .talents-logo {
                height: 45px;
            }
            
            .title {
                font-size: 22px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .greeting {
                font-size: 20px;
            }
            
            .intro-text {
                font-size: 16px;
            }
            
            .register-button {
                padding: 15px 35px;
                font-size: 16px;
            }
            
            .steps-container, .features-section {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container gmail-fix">
        <div class="header">
            <div class="logos-container">
                <div class="logo-fallback">
                    <img src="{{ config('app.url') }}/logos/divergents_logo.png" alt="Divergents" class="logo divergents-logo" style="height: 70px; width: auto;">
                </div>
                <div class="logo-fallback">
                    <img src="{{ config('app.url') }}/logos/talents_lab_logo.png" alt="Talents Lab" class="logo talents-logo" style="height: 55px; width: auto;">
                </div>
            </div>
            <h1 class="title" style="color: #ffffff; font-size: 28px; font-weight: 700; margin: 0;">Ваша анкета добавлена в систему TalentsLab!</h1>
        </div>

        <div class="content">
            <div class="greeting">
                Здравствуйте, {{ $candidate->full_name ?? 'Уважаемый кандидат' }}!
            </div>

            <div class="intro-text">
                Мы рады сообщить вам, что ваша анкета была успешно добавлена в систему TalentsLab.<br>
                Для доступа к системе вам необходимо <strong>зарегистрироваться</strong> с вашим email адресом.
            </div>

            <div class="email-highlight">
                <div class="email-label">Ваш Email для регистрации:</div>
                <div class="email-value">{{ $candidate->email }}</div>
            </div>

            <div class="steps-container">
                <div class="steps-title">Что делать дальше:</div>
                <ol class="steps-list-fallback">
                    <li>Перейдите по ссылке ниже</li>
                    <li>Нажмите "Создать аккаунт"</li>
                    <li>Используйте ваш email: <strong>{{ $candidate->email }}</strong></li>
                    <li>Придумайте надежный пароль</li>
                    <li>После регистрации ваша анкета будет автоматически связана с аккаунтом</li>
                </ol>
            </div>

            <div class="cta-section">
                <a href="{{ config('app.url') }}/register" class="register-button" style="display: inline-block; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #ffffff; padding: 18px 45px; text-decoration: none; border-radius: 50px; font-weight: 700; font-size: 18px; box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);">Зарегистрироваться</a>
            </div>

            <p style="text-align: center; color: #64748b; font-size: 16px; margin: 30px 0;">
                Если у вас возникнут вопросы или проблемы со входом в систему, пожалуйста, свяжитесь с нами.
            </p>
        </div>

        <div class="footer">
            <div class="footer-text">
                С уважением,<br>Команда <strong>Divergents</strong> и <strong>TalentsLab</strong>
            </div>
            <div class="footer-note">
                Это автоматическое сообщение, не отвечайте на него.
            </div>
        </div>
    </div>
</body>
</html>
