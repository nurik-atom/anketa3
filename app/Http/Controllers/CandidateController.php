<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CandidateController extends Controller
{
    public function create($id = null)
    {
        // Если ID передан, проверяем права доступа
        if ($id) {
            $candidate = Candidate::findOrFail($id);
            
            // Проверяем права доступа:
            // 1. Пользователь может редактировать только свои анкеты
            // 2. Администратор может редактировать любые анкеты
            if ($candidate->user_id !== auth()->id() && !auth()->user()->is_admin) {
                abort(403, 'У вас нет прав для редактирования этой анкеты.');
            }
        }
        
        return view('candidate.form', ['candidateId' => $id]);
    }

    public function test()
    {
        return view('candidate.test');
    }
} 