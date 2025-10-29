<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заполни анкету заново - Talents Lab</title>
</head>
<body style="margin: 0; padding: 20px; background-color: #f5f5f5; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #ddd;">
        
        <!-- Header -->
        <div style="background-color: #ffffff; padding: 30px; text-align: center; border-bottom: 1px solid #ddd;">
            <div style="margin-bottom: 20px;">
                <img src="{{ config('app.url') }}/logos/divergents_logo.png" alt="Divergents" style="height: 50px; margin-right: 20px; vertical-align: middle;">
                <img src="{{ config('app.url') }}/logos/talents_lab_logo.png" alt="Talents Lab" style="height: 40px; vertical-align: middle;">
            </div>
            <h1 style="color: #333; font-size: 28px; margin: 0; font-weight: bold;">Привет, Талант!🚀</h1>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <p style="color: #333; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                Помнишь, ты когда-то заполнял(а) анкету Talents Lab в Google-форме?
            </p>

            <p style="color: #333; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                Теперь всё стало проще: мы запустили новый сайт, где можно заполнить анкету заново - в удобном формате.
            </p>

            <!-- Why Important Box -->
            <div style="background-color: #f8f9fa; border-left: 4px solid #2563eb; padding: 20px; margin: 30px 0;">
                <h3 style="color: #333; font-size: 18px; margin: 0 0 15px 0;">Почему важно заполнить снова:</h3>
                
                <div style="margin: 12px 0; color: #333; font-size: 16px; line-height: 1.5;">
                    <span style="color: #2563eb; font-weight: bold; margin-right: 8px;">•</span>
                    чтобы мы могли подобрать тебе вакансии, которые реально подойдут под твой опыт и психометрический портрет;
                </div>
                
                <div style="margin: 12px 0; color: #333; font-size: 16px; line-height: 1.5;">
                    <span style="color: #2563eb; font-weight: bold; margin-right: 8px;">•</span>
                    чтобы учесть твой Gallup-профиль и MBTI;
                </div>
                
                <div style="margin: 12px 0; color: #333; font-size: 16px; line-height: 1.5;">
                    <span style="color: #2563eb; font-weight: bold; margin-right: 8px;">•</span>
                    чтобы наши предложения были максимально точными✅
                </div>
            </div>

            <p style="color: #333; font-size: 16px; line-height: 1.6; margin: 30px 0 20px 0; text-align: center; font-weight: bold;">
                Заполни обновлённую анкету по ссылке👇
            </p>

            <!-- Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ config('app.url') }}/register" style="display: inline-block; background-color: #2563eb; color: #ffffff; padding: 18px 40px; text-decoration: none; font-weight: bold; font-size: 18px; border-radius: 8px;">Заполнить анкету</a>
            </div>

            <!-- Email Box -->
            <div style="background-color: #f8f9fa; border: 2px solid #2563eb; padding: 20px; margin: 30px 0; text-align: center; border-radius: 8px;">
                <div style="color: #333; font-size: 14px; margin-bottom: 10px;">Для входа используй этот email:</div>
                <div style="color: #2563eb; font-size: 18px; font-weight: bold; background-color: #ffffff; padding: 10px; border: 1px solid #ddd; display: inline-block; border-radius: 4px;">{{ $displayEmail }}</div>
            </div>

            <p style="color: #666; font-size: 14px; text-align: center; margin: 20px 0 0 0; line-height: 1.5;">
                Если у тебя возникнут вопросы, свяжись с нами.<br>
                Мы всегда рады помочь!
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
