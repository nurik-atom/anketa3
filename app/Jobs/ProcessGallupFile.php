<?php

namespace App\Jobs;

use App\Http\Controllers\GallupController;
use App\Models\Candidate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessGallupFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $candidate;

    /**
     * Create a new job instance.
     */
    public function __construct(Candidate $candidate)
    {
        $this->candidate = $candidate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting Gallup file processing', [
                'candidate_id' => $this->candidate->id,
                'candidate_name' => $this->candidate->full_name,
                'gallup_pdf' => $this->candidate->gallup_pdf
            ]);

            // Проверяем, что у кандидата есть Gallup PDF файл
            if (!$this->candidate->gallup_pdf) {
                Log::warning('Candidate has no Gallup PDF file', [
                    'candidate_id' => $this->candidate->id
                ]);
                return;
            }

            // Создаем экземпляр GallupController и вызываем метод обработки
            $gallupController = app(GallupController::class);
            $result = $gallupController->parseGallupFromCandidateFile($this->candidate);

            // Проверяем результат обработки
            if ($result instanceof \Illuminate\Http\JsonResponse) {
                $data = $result->getData(true);

                if ($result->getStatusCode() === 200) {
                    Log::info('Gallup file processed successfully', [
                        'candidate_id' => $this->candidate->id,
                        'message' => $data['message'] ?? 'Success'
                    ]);
                } else {
                    Log::error('Gallup file processing failed', [
                        'candidate_id' => $this->candidate->id,
                        'error' => $data['error'] ?? 'Unknown error',
                        'status_code' => $result->getStatusCode()
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Error processing Gallup file', [
                'candidate_id' => $this->candidate->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Повторно бросаем исключение для обработки механизмом очередей Laravel
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Gallup file processing job failed', [
            'candidate_id' => $this->candidate->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
