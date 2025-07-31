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

    public function mount(Candidate $candidate)
    {
        $this->candidate = $candidate;
        $this->url = Storage::url($candidate->anketa_pdf);
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
        return 'view-candidate-pdf/{candidate}';
    }
}
