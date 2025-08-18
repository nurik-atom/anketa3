<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Candidate;

class MigrateFamilyStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'family:migrate-structure {--dry-run : Показать что будет изменено без сохранения}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Мигрирует старую структуру family_members к новой структуре с категориями';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('Начинаем миграцию структуры семьи...');
        
        $candidates = Candidate::whereNotNull('family_members')->get();
        
        $migratedCount = 0;
        $skippedCount = 0;
        
        foreach ($candidates as $candidate) {
            $familyData = $candidate->family_members;
            
            // Проверяем, есть ли уже новая структура
            if (is_array($familyData) && isset($familyData['parents'])) {
                $this->line("ID {$candidate->id}: Уже имеет новую структуру, пропускаем");
                $skippedCount++;
                continue;
            }
            
            // Мигрируем старую структуру
            $newStructure = $this->migrateOldStructure($familyData);
            
            if ($isDryRun) {
                $this->line("ID {$candidate->id}: Будет мигрирован");
                $this->line("  Старые данные: " . json_encode($familyData, JSON_UNESCAPED_UNICODE));
                $this->line("  Новые данные: " . json_encode($newStructure, JSON_UNESCAPED_UNICODE));
                $this->line("");
            } else {
                $candidate->family_members = $newStructure;
                $candidate->save();
                $this->line("ID {$candidate->id}: Мигрирован успешно");
            }
            
            $migratedCount++;
        }
        
        if ($isDryRun) {
            $this->info("Dry-run завершен:");
        } else {
            $this->info("Миграция завершена:");
        }
        
        $this->info("- Мигрировано: {$migratedCount}");
        $this->info("- Пропущено: {$skippedCount}");
        $this->info("- Общий итог: " . ($migratedCount + $skippedCount) . " записей");
    }
    
    /**
     * Мигрирует старую структуру к новой
     */
    private function migrateOldStructure($oldData)
    {
        $parents = [];
        $siblings = [];
        $children = [];
        
        if (!is_array($oldData)) {
            return ['parents' => $parents, 'siblings' => $siblings, 'children' => $children];
        }
        
        foreach ($oldData as $member) {
            if (!is_array($member)) continue;
            
            $type = $member['type'] ?? '';
            
            switch ($type) {
                case 'Отец':
                case 'Мать':
                    $parents[] = [
                        'relation' => $type,
                        'birth_year' => $member['birth_year'] ?? '',
                        'profession' => $member['profession'] ?? ''
                    ];
                    break;
                    
                case 'Брат':
                case 'Сестра':
                    $siblings[] = [
                        'relation' => $type,
                        'birth_year' => $member['birth_year'] ?? '',
                    ];
                    break;
                    
                case 'Сын':
                case 'Дочь':
                    $children[] = [
                        'name' => $member['profession'] ?? '', // В старой структуре имя было в поле profession
                        'birth_year' => $member['birth_year'] ?? '',
                    ];
                    break;
            }
        }
        
        return [
            'parents' => $parents,
            'siblings' => $siblings,
            'children' => $children
        ];
    }
}
