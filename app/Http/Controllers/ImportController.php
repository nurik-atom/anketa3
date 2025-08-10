<?php
namespace App\Http\Controllers;

use App\Models\ImportCandidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    public function importXlsx(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:51200',
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $headers = array_map(fn($h) => trim((string)$h), array_shift($rows));

        $batch = [];
        foreach ($rows as $row) {
            if (!array_filter($row)) continue;

            $assoc = [];
            foreach ($headers as $i => $header) {
                $assoc[$header ?: 'col_' . ($i + 1)] = $row[$i] ?? null;
            }

            $batch[] = [
                'status' => 'new',
                'json_data' => json_encode($assoc, JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('import_candidates')->insert($batch);

        return response()->json([
            'message' => 'Импорт завершен',
            'rows' => count($batch),
        ]);
    }
}
