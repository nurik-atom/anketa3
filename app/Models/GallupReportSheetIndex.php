<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GallupReportSheetIndex extends Model
{
    protected $fillable = [
        'gallup_report_sheet_id',
        'type',
        'name',
        'index',
    ];

    public function gallupReportSheet(): BelongsTo
    {
        return $this->belongsTo(GallupReportSheet::class);
    }
}
