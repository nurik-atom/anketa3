<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public function create($id = null)
    {
        return view('candidate.form', ['candidateId' => $id]);
    }

    public function test()
    {
        return view('candidate.test');
    }
} 