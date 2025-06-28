<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GallupReportSheetValue extends Model
{
    protected $fillable = [
        'gallup_report_sheet_id',
        'candidate_id',
        'user_id',
        'type',
        'name',
        'value',
    ];

    public function gallupReportSheet()
    {
        return $this->belongsTo(GallupReportSheet::class);
    }
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
