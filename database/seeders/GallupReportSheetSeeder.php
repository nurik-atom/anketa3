<?php

namespace Database\Seeders;

use App\Models\GallupReportSheet;
use App\Models\GallupReportSheetIndex;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GallupReportSheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем DPs отчет
        $dpsSheet = GallupReportSheet::create([
            'name_report' => 'DPs',
            'spreadsheet_id' => '1k8RZfrwWyivGJqLZ9IXYsBi55tyX4oGZDsVtTWRR1Xw',
            'gid' => '1270262254'
        ]);

        // Создаем DPT отчет
        $dptSheet = GallupReportSheet::create([
            'name_report' => 'DPT',
            'spreadsheet_id' => '1abc123def456789',
            'gid' => '987654321'
        ]);

        // Создаем FMD отчет
        $fmdSheet = GallupReportSheet::create([
            'name_report' => 'FMD',
            'spreadsheet_id' => '1xyz789uvw012345',
            'gid' => '123456789'
        ]);

        // Список всех 34 талантов Gallup
        $talents = [
            'Achiever', 'Discipline', 'Arranger', 'Focus', 'Belief', 'Responsibility',
            'Consistency', 'Restorative', 'Deliberative', 'Activator', 'Maximizer',
            'Command', 'Self-Assurance', 'Communication', 'Significance', 'Competition', 'Woo',
            'Adaptability', 'Includer', 'Connectedness', 'Individualization', 'Developer',
            'Positivity', 'Empathy', 'Relator', 'Harmony', 'Analytical', 'Input',
            'Context', 'Intellection', 'Futuristic', 'Learner', 'Ideation', 'Strategic'
        ];

        // Соответствующие колонки в Google Sheets (начинаем с C, т.к. B - имя кандидата)
        $columns = [
            'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S',
            'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ'
        ];

        // Добавляем индексы для DPs листа
        foreach ($talents as $index => $talent) {
            GallupReportSheetIndex::create([
                'gallup_report_sheet_id' => $dpsSheet->id,
                'type' => 'talent',
                'name' => $talent,
                'index' => $columns[$index]
            ]);
        }

        // Добавляем некоторые индексы для DPT (пример - только топ-5 талантов)
        $topTalents = array_slice($talents, 0, 5);
        $topColumns = array_slice($columns, 0, 5);

        foreach ($topTalents as $index => $talent) {
            GallupReportSheetIndex::create([
                'gallup_report_sheet_id' => $dptSheet->id,
                'type' => 'talent',
                'name' => $talent,
                'index' => $topColumns[$index]
            ]);
        }

        // Добавляем темы для FMD отчета
        $themes = [
            'Executing' => 'E',
            'Influencing' => 'F',
            'Relationship Building' => 'G',
            'Strategic Thinking' => 'H'
        ];

        foreach ($themes as $theme => $column) {
            GallupReportSheetIndex::create([
                'gallup_report_sheet_id' => $fmdSheet->id,
                'type' => 'theme',
                'name' => $theme,
                'index' => $column
            ]);
        }
    }
}
