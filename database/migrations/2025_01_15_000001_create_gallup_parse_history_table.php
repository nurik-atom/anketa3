<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gallup_parse_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->string('step');
            $table->text('details')->nullable();
            $table->string('status')->default('in_progress'); // in_progress, completed, error
            $table->timestamp('created_at');
            
            $table->index(['candidate_id', 'created_at']);
            $table->index('created_at'); // для быстрой очистки старых записей
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallup_parse_history');
    }
};
