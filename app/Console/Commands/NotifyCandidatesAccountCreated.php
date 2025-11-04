<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Candidate;
use App\Mail\CandidateAccountCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotifyCandidatesAccountCreated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'candidates:notify 
                        {--limit=100 : Количество записей import_candidates для обработки за раз} 
                        {--dry-run : Только показать без отправки email}
                        {--email= : Отправить только конкретному email (из json_data)}
                        {--candidate-id= : Отправить только конкретной записи import_candidates по ID}
                        {--test-email= : Отправить тестовый email на указанный адрес (использует первую подходящую запись)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправить уведомления по адресам из import_candidates.json_data и отметить email_sended';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $dryRun = (bool) $this->option('dry-run');
        $email = $this->option('email');
        $recordId = $this->option('candidate-id');
        $testEmail = $this->option('test-email');

        $this->info('Начинаю обработку записей import_candidates...');

        // Сбор записей из import_candidates
        if ($testEmail) {
            $this->info("Режим тестирования: будет отправлено на {$testEmail}");
            // Возьмем первую запись с валидным email в json_data
            $rows = DB::table('import_candidates')
                ->when($recordId, fn($q) => $q->where('id', $recordId))
                ->limit($limit > 0 ? $limit : 1)
                ->get();
        } else {
            $query = DB::table('import_candidates')
                ->where(function ($q) {
                    $q->whereNull('email_sended')->orWhere('email_sended', 0);
                });

            if ($recordId) {
                $query->where('id', $recordId);
            }

            if ($email) {
                // Фильтр по email внутри json_data (MySQL JSON_EXTRACT)
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.email')) = ?", [$email]);
            }

            if (!$email && !$recordId) {
                $query->limit($limit);
            }

            $rows = $query->get();
        }

        if ($rows->isEmpty()) {
            $this->info('Нет записей для обработки.');
            return;
        }

        // Подготовим коллекцию с извлеченным email из json_data
        $candidates = $rows->map(function ($row) {
            $data = null;
            try {
                $data = json_decode($row->json_data ?? '', true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                $data = json_decode($row->json_data ?? '', true) ?: null; // fallback без исключений
            }
            $email = is_array($data) ? trim((string)($data['email'] ?? '')) : '';
            $fullName = is_array($data) ? trim((string)($data['full_name'] ?? '')) : '';
            return (object) [
                'id' => $row->id,
                'email' => $email,
                'full_name' => $fullName,
                'email_sended' => $row->email_sended ?? 0,
            ];
        })->filter(function ($c) use ($testEmail, $email) {
            // В тестовом режиме пропускаем записи без email; при фильтре по email — уже отфильтровано
            if ($testEmail) {
                return !empty($c->email);
            }
            if ($email) {
                return !empty($c->email) && strcasecmp($c->email, $email) === 0;
            }
            return !empty($c->email);
        })->values();

        if ($candidates->isEmpty()) {
            $this->info('Подходящих записей с email не найдено.');
            return;
        }

        $this->info("Найдено {$candidates->count()} записей для отправки уведомлений.");

        if ($dryRun) {
            $this->table(
                ['ID', 'Email (из json)', 'email_sended'],
                $candidates->map(fn($c) => [
                    $c->id,
                    $c->email,
                    (int) $c->email_sended,
                ])
            );
            $this->info('Режим тестирования - email не отправлены.');
            return;
        }

        $sent = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($candidates->count());

        foreach ($candidates as $c) {
            $recipientEmail = $testEmail ?: $c->email;

            try {
                // Сформируем временного кандидата для Mailable (тип ожидается Candidate)
                $tempCandidate = new Candidate();
                $tempCandidate->email = $c->email;
                $tempCandidate->full_name = $c->full_name;

                Mail::to($recipientEmail)
                    ->send(new CandidateAccountCreated($tempCandidate, 'A123456a', $testEmail ?: $c->email));

                // Помечаем запись как отправленную
                DB::table('import_candidates')->where('id', $c->id)->update(['email_sended' => 1]);

                // Логируем успешную отправку
                Log::channel('email_send')->info("Email sent", [
                    'import_candidate_id' => $c->id,
                    'to' => $recipientEmail,
                ]);

                $sent++;
                $progressBar->advance();

            } catch (\Exception $e) {
                $errors++;

                // Логируем ошибку
                Log::channel('email_send')->error("Email send failed", [
                    'import_candidate_id' => $c->id,
                    'to' => $recipientEmail,
                    'error' => $e->getMessage(),
                ]);

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
