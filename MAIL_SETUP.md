# Настройка почты для Laravel приложения

## Проблема
Gmail отклоняет письма из-за проблем с IPv6 PTR записями и аутентификацией вашего хостинг-провайдера (31537-isp.kz).

## Решение

### 1. Создайте файл .env на сервере
Создайте файл `.env` в корне проекта со следующими настройками:

```env
APP_NAME=Divergents
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

# Настройки почты для Gmail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-gmail@gmail.com
MAIL_FROM_NAME="Divergents"

# Остальные настройки...
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

### 2. Настройка Gmail

#### Шаг 1: Создайте App Password в Gmail
1. Войдите в свой Gmail аккаунт
2. Перейдите в настройки безопасности: https://myaccount.google.com/security
3. Включите двухфакторную аутентификацию (если не включена)
4. Создайте "App Password" для приложения
5. Используйте этот пароль в `MAIL_PASSWORD`

#### Шаг 2: Альтернативные варианты

**Вариант A: Использовать другой SMTP сервис**
```env
# Для Mailgun
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-mailgun-username
MAIL_PASSWORD=your-mailgun-password
MAIL_ENCRYPTION=tls

# Для SendGrid
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

**Вариант B: Использовать почту хостинга**
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Divergents"
```

### 3. Проверка настроек

После настройки .env файла выполните:

```bash
# Очистите кэш конфигурации
php artisan config:clear
php artisan config:cache

# Проверьте настройки почты
php artisan tinker
>>> config('mail')
```

### 4. Тестирование отправки

Создайте тестовую команду для проверки отправки:

```bash
php artisan make:command TestMail
```

В файле `app/Console/Commands/TestMail.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    protected $signature = 'test:mail {email}';
    protected $description = 'Test email sending';

    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            Mail::raw('Test email from Laravel', function ($message) use ($email) {
                $message->to($email)->subject('Test Email');
            });
            
            $this->info('Email sent successfully!');
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
        }
    }
}
```

Запустите тест:
```bash
php artisan test:mail your-email@gmail.com
```

### 5. Дополнительные рекомендации

1. **Используйте доменную почту**: Лучше использовать почту с вашего домена (например, noreply@yourdomain.com)
2. **Настройте SPF и DKIM записи** в DNS для улучшения доставляемости
3. **Рассмотрите использование сервисов** типа Mailgun, SendGrid или Amazon SES для надежной доставки

### 6. Настройка DNS записей (рекомендуется)

Добавьте в DNS вашего домена:

```
# SPF запись
TXT @ "v=spf1 include:_spf.google.com ~all"

# DKIM запись (получите от вашего почтового провайдера)
TXT default._domainkey "v=DKIM1; k=rsa; p=YOUR_PUBLIC_KEY"

# DMARC запись
TXT _dmarc "v=DMARC1; p=quarantine; rua=mailto:dmarc@yourdomain.com"
```
