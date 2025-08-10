<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImportCandidateService;

class ImportOneCandidate extends Command
{
    protected $signature = 'import:candidate-one';
    protected $description = 'Импортировать одного кандидата из import_candidates (status=new)';

    public function handle(ImportCandidateService $service): int
    {
        $service->importOne();
        $this->info('Done (one attempt).');
        return self::SUCCESS;
    }
}
