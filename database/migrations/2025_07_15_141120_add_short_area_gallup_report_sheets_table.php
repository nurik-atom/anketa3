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
        Schema::table('gallup_report_sheets', function (Blueprint $table) {
            $table->string('short_area')->nullable()->after('gid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gallup_report_sheets', function (Blueprint $table) {
            $table->dropColumn('short_area');
        });
    }
};
