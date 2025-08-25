<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ваш аккаунт создан</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4A90E2;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 200px;
            height: auto;
        }
        .title {
            color: #4A90E2;
            margin: 20px 0 10px 0;
            font-size: 24px;
        }
        .credentials-box {
            background-color: #f8f9fa;
            border: 2px solid #4A90E2;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .credential-item {
            margin: 10px 0;
            font-size: 16px;
        }
        .credential-label {
            font-weight: bold;
            color: #333;
        }
        .credential-value {
            font-family: 'Courier New', monospace;
            background-color: #ffffff;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 5px 0;
            display: inline-block;
            color: #2c3e50;
        }
        .login-button {
            display: inline-block;
            background-color: #4A90E2;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .login-button:hover {
            background-color: #357abd;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #666;
            text-align: center;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Добро пожаловать в систему Anketa!</h1>
        </div>

        <div class="greeting">
            Здравствуйте, {{ $candidate->full_name ?? 'Уважаемый кандидат' }}!
        </div>

        <p>Мы рады сообщить вам, что ваш аккаунт в системе Anketa был успешно создан.</p>

        <p>Теперь вы можете войти в систему, используя следующие данные:</p>

        <div class="credentials-box">
            <div class="credential-item">
                <div class="credential-label">Логин (Email):</div>
                <div class="credential-value">{{ $candidate->email }}</div>
            </div>
            <div class="credential-item">
                <div class="credential-label">Пароль:</div>
                <div class="credential-value">{{ $password }}</div>
            </div>
        </div>

        <div class="warning">
            <strong>Важно!</strong> Рекомендуем вам изменить пароль после первого входа в систему для обеспечения безопасности вашего аккаунта.
        </div>

        <div style="text-align: center;">
            <a href="{{ config('app.url') }}/login" class="login-button">Войти в систему</a>
        </div>

        <p>После входа в систему вы сможете:</p>
        <ul>
            <li>Просматривать и редактировать свой профиль</li>
            <li>Проходить тестирования</li>
            <li>Просматривать результаты оценок</li>
            <li>Взаимодействовать с нашей командой</li>
        </ul>

        <p>Если у вас возникнут вопросы или проблемы со входом в систему, пожалуйста, свяжитесь с нами.</p>

        <div class="footer">
            <p>С уважением,<br>Команда Anketa</p>
            <p><strong>Это автоматическое сообщение, не отвечайте на него.</strong></p>
        </div>
    </div>
</body>
</html>
