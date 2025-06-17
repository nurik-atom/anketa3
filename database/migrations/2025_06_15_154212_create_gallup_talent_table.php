<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gallup_talents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->string('name');           // Название таланта (например, Intellection)
            $table->unsignedTinyInteger('position'); // Позиция от 1 до 34
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallup_talents');
    }
};
