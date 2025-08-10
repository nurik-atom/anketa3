<?php

namespace App\Services;

use App\Http\Controllers\GallupController;
use App\Jobs\ProcessGallupFile;
use App\Models\User;
use App\Models\Candidate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class ImportCandidateService
{
    public function importOne(): void
    {
        // Берём одну «new» запись и лочим её транзакцией
        DB::transaction(function () {
            $row = DB::table('import_candidates')
                ->where('status', 'new')
                ->where('id' ,  952) // Убедимся, что есть хотя бы одна запись
                ->lockForUpdate()
                ->orderBy('id')
                ->first();

            if (!$row) {
                return; // Нечего импортировать
            }

            $data = json_decode($row->json_data ?? '{}', true) ?: [];

            try {
                // 1) Создаём/находим пользователя
                $email = trim((string)($data['email'] ?? ''));
                if (!$email) {
                    throw new \RuntimeException('email пустой');
                }

                $fullName = trim((string)($data['full_name'] ?? ''));
                $user = User::where('email', $email)->first();

                if (!$user) {
                    $user = new User();
                    $user->name = $fullName ?: ($data['desired_position'] ?? 'Candidate');
                    $user->email = $email;
                    $user->password = 'A123456a';
                    $user->email_verified_at = now();
                    $user->save();
                }

                // 2) Подготавливаем поля кандидата
                Candidate::where('user_id', $user->id)->delete();

                $candidate = new Candidate();
                $candidate->user_id = $user->id;
                $candidate->save();

                // Утилиты
                $normBool = function ($val) {
                    $v = mb_strtolower(trim((string)$val));
                    return in_array($v, ['1','да','есть','true','yes','y'], true) ? 1 : 0;
                };
                $parseDate = function ($val) {
                    // В источнике часто M/D/Y или D/M/Y. Попробуем оба.
                    $s = trim((string)$val);
                    if ($s === '') return null;
                    foreach (['m/d/Y', 'd/m/Y', 'd.m.Y', 'Y-m-d'] as $fmt) {
                        try {
                            $c = Carbon::createFromFormat($fmt, $s);
                            if ($c !== false) return $c->format('Y-m-d');
                        } catch (\Throwable) {}
                    }
                    // Последняя попытка — Carbon::parse
                    try {
                        return Carbon::parse($s)->format('Y-m-d');
                    } catch (\Throwable) {
                        return null;
                    }
                };

                // 3) Скачиваем файлы с Google Drive (если есть)
                $gallupPdfPath = null;
                if (!empty($data['gallup_pdf'])) {
                    $gallupPdfPath = $this->downloadFromGoogleDrive($data['gallup_pdf'], "gallup/candidate_{$candidate->id}.pdf");
                }

                $photoPath = null;
                if (!empty($data['photo'])) {
                    $photoPath = $this->downloadFromGoogleDrive($data['photo'], "photos/candidate_{$candidate->id}.jpg");
                }

                // 4) Заполняем модель Candidate
                $candidate->full_name = $fullName ?: $user->name;
                $candidate->email = $email;
                $candidate->phone = (string)($data['phone'] ?? null);

                // Приведём «Мужчина» -> «Мужской»
                $genderSrc = trim((string)($data['gender'] ?? ''));
                $candidate->gender = $genderSrc === 'Мужчина' ? 'Мужской' : ($genderSrc === 'Женщина' ? 'Женский' : $genderSrc);

                $candidate->marital_status = (string)($data['marital_status'] ?? null);
                $candidate->birth_date = $parseDate($data['birth_date'] ?? null);
                $candidate->birth_place = (string)($data['birth_place'] ?? null);
                $candidate->current_city = (string)($data['current_city'] ?? null);
                $candidate->religion = (string)($data['religion'] ?? null);
                $candidate->is_practicing = isset($data['is_practicing']) ? $normBool($data['is_practicing']) : null;

                $candidate->school = (string)($data['school'] ?? null);
                $candidate->universities = (string)($data['universities'] ?? null);

                $candidate->computer_skills = (string)($data['computer_skills'] ?? null);
                $candidate->employer_requirements = (string)($data['employer_requirements'] ?? null);
                $candidate->desired_position = (string)($data['desired_position'] ?? null);
                $candidate->mbti_type = (string)($data['mbti_type'] ?? null);

                $candidate->hobbies = (string)($data['hobbies'] ?? null);
                $candidate->interests = (string)($data['interests'] ?? null);
                $candidate->has_driving_license = isset($data['has_driving_license']) ? $normBool($data['has_driving_license']) : null;

                if ($gallupPdfPath) {
                    $candidate->gallup_pdf = $gallupPdfPath; // относительный путь в диске public
                }
                if ($photoPath) {
                    $candidate->photo = $photoPath; // относительный путь в диске public
                }

                // Остальные поля из вашей схемы оставляем null/дефолт, либо добавьте маппинг при необходимости
                $candidate->step = $candidate->step ?? 1;

                $candidate->save();

                if ( $gallupPdfPath ){
                    ProcessGallupFile::dispatch($candidate);
                    logger()->info('Gallup file processing job dispatched', [
                        'candidate_id' => $candidate->id,
                        'gallup_pdf' => $candidate->gallup_pdf
                    ]);
                    $status = 'success';
                }else{
                    $status = 'no_gallup_pdf';
                }


                // 5) Отмечаем запись как обработанную
                DB::table('import_candidates')->where('id', $row->id)->update([
                    'status' => $status,
                    'updated_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // Логируем в отдельный файл
                Log::build([
                    'driver' => 'single',
                    'path' => storage_path('logs/import_candidates.log'),
                ])->error(sprintf(
                    "[%s] Import error for import_candidate_id=%d, email=%s; payload=%s; error=%s\nTrace:\n%s",
                    now()->toDateTimeString(),
                    $row->id,
                    $data['email'] ?? '-',
                    json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                    $e->getMessage(),
                    $e->getTraceAsString()
                ));

                // Помечаем ошибку (если хотите — добавьте колонку error_message в таблицу)
                DB::table('import_candidates')->where('id', $row->id)->update([
                    'status' => 'failed',
                    'updated_at' => now(),
                ]);
                // По желанию — залогировать
                report($e);
            }
        });
    }

    /**
     * Скачивает файл из Google Drive по “open?id=...” или “/file/d/.../view” ссылке.
     * Сохраняет в storage/app/public/$destRelativePath и возвращает относительный путь (для диска public).
     */
    private function downloadFromGoogleDrive(string $url, string $destRelativePath): ?string
    {
        try {
            // 1) Определяем direct URL
            $directUrl = null;

            // уже прямой линк (например, drive.usercontent.google.com/download?id=...)
            if (preg_match('~^https?://drive\.usercontent\.google\.com/.*~i', $url)) {
                $directUrl = $url;
            } else {
                // open?id=... или /file/d/{id}/...
                $fileId = $this->extractGoogleFileId($url);
                if (!$fileId) return null;
                $directUrl = "https://drive.google.com/uc?export=download&id={$fileId}";
            }

            // 2) Скачиваем
            $resp = \Illuminate\Support\Facades\Http::timeout(60)
                ->withHeaders(['User-Agent' => 'Laravel-Importer'])
                ->get($directUrl);

            if (!$resp->ok()) {
                return null; // 4xx/5xx -> пропускаем
            }

            $ct  = strtolower((string) $resp->header('Content-Type', ''));
            $bin = $resp->body();

            if ($bin === '' || $bin === null) {
                return null;
            }

            // 3) Отсекаем HTML (403/логин/квота)
            $head = ltrim(substr($bin, 0, 1024));
            if (str_starts_with($head, '<!doctype html') || str_starts_with($head, '<html')) {
                return null;
            }
            if (str_contains($ct, 'text/html')) {
                return null;
            }

            // 4) MIME-sniffing по сигнатуре
            $isPdf  = str_starts_with($bin, '%PDF');
            $isJpeg = substr($bin, 0, 3) === "\xFF\xD8\xFF";
            $isPng  = substr($bin, 0, 8) === "\x89PNG\x0D\x0A\x1A\x0A";

            // Если сигнатура не распознана, последняя надежда — заголовок
            $looksLikeBinaryWeNeed = $isPdf || $isJpeg || $isPng
                || str_contains($ct, 'application/pdf')
                || str_starts_with($ct, 'image/');

            if (!$looksLikeBinaryWeNeed) {
                return null;
            }

            // 5) Расширение
            $ext = null;
            if     ($isPdf || str_contains($ct, 'pdf'))   $ext = 'pdf';
            elseif ($isJpeg || str_contains($ct, 'jpeg')) $ext = 'jpg';
            elseif ($isPng  || str_contains($ct, 'png'))  $ext = 'png';

            if ($ext && !preg_match('/\.' . preg_quote($ext, '/') . '$/i', $destRelativePath)) {
                $destRelativePath .= '.' . $ext;
            }

            \Illuminate\Support\Facades\Storage::disk('public')->put($destRelativePath, $bin);
            return $destRelativePath;
        } catch (\Throwable $e) {
            // Можно залогировать предупреждение, но импорт не валим
            Log::build(['driver'=>'single','path'=>storage_path('logs/import_candidates.log')])
                ->warning('downloadFromGoogleDrive error: '.$e->getMessage().' url='.$url);
            return null;
        }
    }

    private function extractGoogleFileId(string $url): ?string
    {
        // Вариант 1: ...open?id=FILE_ID
        if (preg_match('~[?&]id=([a-zA-Z0-9_-]+)~', $url, $m)) {
            return $m[1];
        }
        // Вариант 2: .../file/d/FILE_ID/view
        if (preg_match('~/file/d/([a-zA-Z0-9_-]+)~', $url, $m)) {
            return $m[1];
        }
        return null;
    }
}
