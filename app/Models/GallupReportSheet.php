<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GallupReportSheet extends Model
{
    protected $fillable = [
        'name_report',
        'spreadsheet_id',
        'gid',
        'short_gid',
    ];

    public function indices(): HasMany
    {
        return $this->hasMany(GallupReportSheetIndex::class);
    }
}
