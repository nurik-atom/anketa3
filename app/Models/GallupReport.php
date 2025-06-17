<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GallupReport extends Model
{
    protected $fillable = ['candidate_id', 'type', 'pdf_file'];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
