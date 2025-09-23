<?php

namespace App\Console\Commands;

use App\Models\GallupParseHistory;
use Illuminate\Console\Command;

class CleanGallupParseHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gallup:clean-history {--days=7 : Количество дней для хранения записей}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очищает старые записи истории парсинга Gallup отчетов';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        
        if ($days < 1) {
            $this->error('Количество дней должно быть больше 0');
            return 1;
        }

        $this->info("Очистка записей истории старше {$days} дней...");

        $deletedCount = GallupParseHistory::cleanOldRecords($days);

        $this->info("Удалено записей: {$deletedCount}");

        if ($deletedCount > 0) {
            $this->info("✅ Очистка завершена успешно");
        } else {
            $this->info("ℹ️  Нет записей для удаления");
        }

        return 0;
    }
}
