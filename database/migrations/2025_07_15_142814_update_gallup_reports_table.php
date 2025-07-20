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
        Schema::table('gallup_reports', function (Blueprint $table) {
            $table->dropColumn(['document_id', 'document_url']);
            $table->renameColumn('word_file', 'short_area_pdf_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gallup_reports', function (Blueprint $table) {
            $table->string('document_id')->nullable();
            $table->string('document_url')->nullable();
            $table->renameColumn('short_area_pdf_file', 'word_file');
        });
    }
};
