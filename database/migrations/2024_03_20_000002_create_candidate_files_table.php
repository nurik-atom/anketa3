<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'photo', 'gallup_pdf', etc.
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_files');
    }
}; 