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
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('anketa_pdf')->after('gallup_pdf')->nullable();
            $table->dropColumn(['education', 'experience']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('anketa_pdf');
            $table->string('education')->after('gallup_pdf')->nullable();
            $table->string('experience')->after('education')->nullable();
        });
    }
};
