<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gallup_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->string('type');       // Тип отчета
            $table->string('pdf_file');   // Путь к PDF
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallup_reports');
    }
};
