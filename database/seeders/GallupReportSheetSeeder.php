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

        // Добавляем индексы для DPT отчета (психотипы)
        $dptPsychotypes = [
            'Паранойял' => 'C3',
            'Эпилептоид' => 'D3',
            'Истероид' => 'E3',
            'Гипертим' => 'F3',
            'Шизоид' => 'G3',
            'Психастеноид' => 'H3',
            'Эмотив' => 'I3',
            'Депрессивный' => 'J3'
        ];

        foreach ($dptPsychotypes as $name => $column) {
            GallupReportSheetIndex::create([
                'gallup_report_sheet_id' => $dptSheet->id,
                'type' => 'Психотипы',
                'name' => $name,
                'index' => $column
            ]);
        }

        // Добавляем индивидуальные качества для DPT
        $dptIndividualQualities = [
            'Интроверсия' => 'N3',
            'Экстраверсия' => 'O3',
            'Прагматизм' => 'P3',
            'Энергия' => 'Q3',
            'Лояльность' => 'R3',
            'Самообразование' => 'S3',
            'Параноик' => 'T3',
            'Нетворкинг' => 'U3',
            'Душа компании' => 'V3',
            'Комплексное мышление' => 'W3',
            'Конвергент' => 'X3',
            'Упрямство' => 'Y3',
            'Скромность' => 'Z3',
            'Креативность' => 'AA3',
            'Конформизм' => 'AB3',
            'Импульсивность' => 'AC3',
            'Чувствительность' => 'AD3',
            'Критическое мышление' => 'AE3'
        ];

        foreach ($dptIndividualQualities as $name => $column) {
            GallupReportSheetIndex::create([
                'gallup_report_sheet_id' => $dptSheet->id,
                'type' => 'Индивидуальные качества',
                'name' => $name,
                'index' => $column
            ]);
        }

        // Добавляем дополнительные параметры для DPT
        $dptAdditional = [
            'Стрессоустойчивость' => 'K3',
            'Лидерство' => 'L3',
            'Коммуникабельность' => 'M3'
        ];

        foreach ($dptAdditional as $name => $column) {
            GallupReportSheetIndex::create([
                'gallup_report_sheet_id' => $dptSheet->id,
                'type' => 'Дополнительные параметры',
                'name' => $name,
                'index' => $column
            ]);
        }

        // Добавляем индексы для FMD отчета
        $fmdCharacteristics = [
            'Физическая выносливость' => 'C4',
            'Умственная работоспособность' => 'D4',
            'Концентрация внимания' => 'E4',
            'Память' => 'F4',
            'Реакция на стресс' => 'G4',
            'Адаптивность' => 'H4'
        ];

        foreach ($fmdCharacteristics as $name => $column) {
            GallupReportSheetIndex::create([
                'gallup_report_sheet_id' => $fmdSheet->id,
                'type' => 'Медицинские показатели',
                'name' => $name,
                'index' => $column
            ]);
        }

        // Добавляем психологические параметры для FMD
        $fmdPsychological = [
            'Тревожность' => 'I4',
            'Депрессивность' => 'J4',
            'Агрессивность' => 'K4',
            'Социальность' => 'L4'
        ];

        foreach ($fmdPsychological as $name => $column) {
            GallupReportSheetIndex::create([
                'gallup_report_sheet_id' => $fmdSheet->id,
                'type' => 'Психологические параметры',
                'name' => $name,
                'index' => $column
            ]);
        }
    }
}
