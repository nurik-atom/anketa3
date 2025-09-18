<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ваша анкета добавлена в TalentsLab</title>
</head>
<body style="margin: 0; padding: 20px; background-color: #f5f5f5; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #ddd;">
        
        <!-- Header -->
        <div style="background-color: #ffffff; padding: 30px; text-align: center; border-bottom: 1px solid #ddd;">
            <div style="margin-bottom: 20px;">
                <img src="{{ config('app.url') }}/logos/divergents_logo.png" alt="Divergents" style="height: 50px; margin-right: 20px; vertical-align: middle;">
                <img src="{{ config('app.url') }}/logos/talents_lab_logo.png" alt="Talents Lab" style="height: 40px; vertical-align: middle;">
            </div>
            <h1 style="color: #333; font-size: 24px; margin: 0; font-weight: bold;">Ваша анкета добавлена в систему TalentsLab!</h1>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <h2 style="color: #333; font-size: 20px; margin: 0 0 20px 0; text-align: center;">
                Здравствуйте, {{ $candidate->full_name ?? 'Уважаемый кандидат' }}!
            </h2>

            <p style="color: #666; font-size: 16px; line-height: 1.5; text-align: center; margin: 0 0 30px 0;">
                Мы рады сообщить вам, что ваша анкета была успешно добавлена в систему TalentsLab.<br>
                Для доступа к системе вам необходимо <strong>зарегистрироваться</strong> с вашим email адресом.
            </p>

            <!-- Email Box -->
            <div style="background-color: #f8f9fa; border: 2px solid #2563eb; padding: 20px; margin: 20px 0; text-align: center;">
                <div style="color: #333; font-size: 16px; margin-bottom: 10px; font-weight: bold;">Ваш Email для регистрации:</div>
                <div style="color: #2563eb; font-size: 18px; font-weight: bold; background-color: #ffffff; padding: 10px; border: 1px solid #ddd; display: inline-block;">{{ $candidate->email }}</div>
            </div>

            <!-- Steps -->
            <div style="background-color: #f8f9fa; border: 1px solid #ddd; padding: 25px; margin: 30px 0;">
                <h3 style="color: #333; font-size: 18px; margin: 0 0 20px 0; text-align: center;">Что делать дальше:</h3>
                
                <div style="margin: 15px 0;">
                    <div style="display: inline-block; background-color: #2563eb; color: white; width: 25px; height: 25px; border-radius: 50%; text-align: center; line-height: 25px; font-weight: bold; margin-right: 15px; vertical-align: top;">1</div>
                    <div style="display: inline-block; width: calc(100% - 50px); color: #333; font-size: 16px; line-height: 1.4;">Перейдите по ссылке ниже</div>
                </div>
                
                <div style="margin: 15px 0;">
                    <div style="display: inline-block; background-color: #2563eb; color: white; width: 25px; height: 25px; border-radius: 50%; text-align: center; line-height: 25px; font-weight: bold; margin-right: 15px; vertical-align: top;">2</div>
                    <div style="display: inline-block; width: calc(100% - 50px); color: #333; font-size: 16px; line-height: 1.4;">Нажмите "Создать аккаунт"</div>
                </div>
                
                <div style="margin: 15px 0;">
                    <div style="display: inline-block; background-color: #2563eb; color: white; width: 25px; height: 25px; border-radius: 50%; text-align: center; line-height: 25px; font-weight: bold; margin-right: 15px; vertical-align: top;">3</div>
                    <div style="display: inline-block; width: calc(100% - 50px); color: #333; font-size: 16px; line-height: 1.4;">Используйте ваш email: <strong>{{ $candidate->email }}</strong></div>
                </div>
                
                <div style="margin: 15px 0;">
                    <div style="display: inline-block; background-color: #2563eb; color: white; width: 25px; height: 25px; border-radius: 50%; text-align: center; line-height: 25px; font-weight: bold; margin-right: 15px; vertical-align: top;">4</div>
                    <div style="display: inline-block; width: calc(100% - 50px); color: #333; font-size: 16px; line-height: 1.4;">Придумайте надежный пароль</div>
                </div>
                
                <div style="margin: 15px 0;">
                    <div style="display: inline-block; background-color: #2563eb; color: white; width: 25px; height: 25px; border-radius: 50%; text-align: center; line-height: 25px; font-weight: bold; margin-right: 15px; vertical-align: top;">5</div>
                    <div style="display: inline-block; width: calc(100% - 50px); color: #333; font-size: 16px; line-height: 1.4;">После регистрации ваша анкета будет автоматически связана с аккаунтом</div>
                </div>
            </div>

            <!-- Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ config('app.url') }}/register" style="display: inline-block; background-color: #2563eb; color: #ffffff; padding: 15px 30px; text-decoration: none; font-weight: bold; font-size: 16px;">Зарегистрироваться</a>
            </div>

            <p style="color: #666; font-size: 14px; text-align: center; margin: 20px 0 0 0;">
                Если у вас возникнут вопросы или проблемы со входом в систему, пожалуйста, свяжитесь с нами.
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #ddd;">
            <div style="color: #666; font-size: 14px; margin-bottom: 5px;">
                С уважением,<br>Команда <strong>Divergents</strong> и <strong>TalentsLab</strong>
            </div>
            <div style="color: #999; font-size: 12px;">
                Это автоматическое сообщение, не отвечайте на него.
            </div>
        </div>
    </div>
</body>
</html>