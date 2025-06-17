<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GallupTalent extends Model
{
    protected $table = 'gallup_talents';
    protected $fillable = ['candidate_id', 'name', 'position'];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
