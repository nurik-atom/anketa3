<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:20480' // максимум 20MB
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = Str::random(40) . '.jpg';
            
            // Сохраняем в storage/app/public/photos
            $path = $file->storeAs('photos', $filename, 'public');
            
            // Сохраняем путь в базу данных
            $user = auth()->user();
            $user->photo = $path;
            $user->save();
            
            return response()->json([
                'success' => true,
                'url' => Storage::url($path)
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Файл не был загружен'
        ], 400);
    }
} 