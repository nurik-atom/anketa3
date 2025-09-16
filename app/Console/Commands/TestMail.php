<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mail {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('Testing email configuration...');
        $this->info('Mail driver: ' . config('mail.default'));
        $this->info('SMTP host: ' . config('mail.mailers.smtp.host'));
        $this->info('SMTP port: ' . config('mail.mailers.smtp.port'));
        $this->info('From address: ' . config('mail.from.address'));
        $this->info('From name: ' . config('mail.from.name'));
        
        try {
            Mail::raw('Это тестовое письмо от Laravel приложения Divergents. Если вы получили это письмо, значит настройки почты работают корректно.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Тестовое письмо - Divergents');
            });
            
            $this->info('✅ Email sent successfully to: ' . $email);
            $this->info('Проверьте папку "Входящие" и "Спам" в вашем почтовом ящике.');
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
            $this->error('Проверьте настройки в .env файле:');
            $this->error('- MAIL_MAILER=smtp');
            $this->error('- MAIL_HOST=smtp.gmail.com');
            $this->error('- MAIL_PORT=587');
            $this->error('- MAIL_USERNAME=your-gmail@gmail.com');
            $this->error('- MAIL_PASSWORD=your-app-password');
            $this->error('- MAIL_ENCRYPTION=tls');
        }
    }
}
