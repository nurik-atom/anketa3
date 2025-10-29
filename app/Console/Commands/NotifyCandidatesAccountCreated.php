<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Candidate;
use App\Mail\CandidateAccountCreated;
use Illuminate\Support\Facades\Mail;

class NotifyCandidatesAccountCreated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'candidates:notify 
                        {--limit=100 : Количество кандидатов для обработки за раз} 
                        {--dry-run : Только показать кандидатов без отправки email}
                        {--email= : Отправить только конкретному email}
                        {--candidate-id= : Отправить только конкретному кандидату по ID}
                        {--test-email= : Отправить тестовый email на указанный адрес (использует первого кандидата)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправить уведомления кандидатам о создании их аккаунтов';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $dryRun = $this->option('dry-run');
        $email = $this->option('email');
        $candidateId = $this->option('candidate-id');
        $testEmail = $this->option('test-email');

        $this->info('Начинаю отправку уведомлений кандидатам...');

        // Если указан test-email, берем первого кандидата для теста
        if ($testEmail) {
            $candidates = collect([Candidate::whereNotNull('email')->first()]);
            if ($candidates->first()) {
                $this->info("Режим тестирования: будет отправлено на {$testEmail}");
            }
        } else {
            // Строим запрос для получения кандидатов
            $query = Candidate::whereNotNull('email')
                ->whereNull('user_id');

            // Фильтруем по email если указан
            if ($email) {
                $query->where('email', $email);
            }

            // Фильтруем по ID кандидата если указан
            if ($candidateId) {
                $query->where('id', $candidateId);
            }

            // Применяем лимит только если не указаны конкретные фильтры
            if (!$email && !$candidateId) {
                $query->limit($limit);
            }

            $candidates = $query->get();
        }

        if ($candidates->isEmpty()) {
            $this->info('Нет кандидатов для отправки уведомлений.');
            return;
        }

        $this->info("Найдено {$candidates->count()} кандидатов для отправки уведомлений.");

        if ($dryRun) {
            $this->table(
                ['ID', 'Имя', 'Email', 'User ID'],
                $candidates->map(function ($candidate) {
                    return [
                        $candidate->id,
                        $candidate->full_name ?? 'Не указано',
                        $candidate->email,
                        $candidate->user_id
                    ];
                })
            );
            $this->info('Режим тестирования - email не отправлены.');
            return;
        }

        $sent = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($candidates->count());

        foreach ($candidates as $candidate) {
            try {
                // Определяем адрес получателя (тестовый или реальный)
                $recipientEmail = $testEmail ?: $candidate->email;
                
                // Отправляем email с учетными данными
                // В тестовом режиме передаем тестовый email для отображения в письме
                Mail::to($recipientEmail)->send(new CandidateAccountCreated($candidate, 'A123456a', $testEmail));
                
                $sent++;
                $progressBar->advance();

            } catch (\Exception $e) {
                $errors++;
                $this->error("\nОшибка при отправке email для {$recipientEmail}: " . $e->getMessage());
                $progressBar->advance();
            }
        }

        $progressBar->finish();

        $this->newLine(2);
        $this->info("Отправка завершена!");
        $this->info("Отправлено: {$sent}");
        $this->info("Ошибки: {$errors}");
    }
}
