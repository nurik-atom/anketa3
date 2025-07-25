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
        Schema::create('gallup_report_sheet_indices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallup_report_sheet_id')->constrained('gallup_report_sheets')->onDelete('cascade');
            $table->string('type');
            $table->string('name');
            $table->string('index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallup_report_sheet_indices');
    }
};
