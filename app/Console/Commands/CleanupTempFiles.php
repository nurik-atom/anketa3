<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupTempFiles extends Command
{
    protected $signature = 'cleanup:temp-files';
    protected $description = 'Удаляет временные файлы старше 1 часа';

    public function handle()
    {
        $tempDir = 'temp_anketas';
        
        // Проверяем, существует ли директория
        if (!Storage::disk('public')->exists($tempDir)) {
            $this->info('Директория временных файлов не существует');
            return;
        }
        
        $files = Storage::disk('public')->files($tempDir);
        $deletedCount = 0;

        foreach ($files as $file) {
            $lastModified = Storage::disk('public')->lastModified($file);
            $fileAge = Carbon::createFromTimestamp($lastModified);

            // Удаляем файлы старше 1 часа
            if ($fileAge->diffInMinutes(now()) > 60) {
                Storage::disk('public')->delete($file);
                $deletedCount++;
                $this->line("Удален файл: {$file}");
            }
        }

        $this->info("Удалено {$deletedCount} временных файлов");
    }
}
