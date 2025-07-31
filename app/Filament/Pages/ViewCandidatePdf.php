<?php
namespace App\Filament\Pages;

use App\Models\Candidate;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class ViewCandidatePdf extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.view-candidate-pdf';

    public ?Candidate $candidate = null;
    public string $url;

    public function mount(Candidate $candidate, string $type)
    {
        $this->candidate = $candidate;
        $this->type = strtoupper($type); // для отображения в заголовке
        if ($type == 'anketa'){
            $this->url = Storage::disk('public')->url($candidate->anketa_pdf);
            dd($this->url);
        }else{
            $report = $candidate->gallupReportByType($type);

//            dd($report->pdf_file);

            abort_if(!$report || !Storage::disk('public')->exists($report->pdf_file), 404);
            $this->url = Storage::url($report->pdf_file);
        }

    }

    public function getTitle(): string
    {
        return "{$this->candidate->full_name} — {$this->type} отчет";
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

//    public static function getRouteName(?string $panel = null): string
//    {
//        return 'filament.' . ($panel ?? 'admin') . '.pages.view-candidate-pdf';
//    }

    public static function getSlug(): string
    {
        return 'view-candidate-pdf/{candidate}/{type}';
    }
}
