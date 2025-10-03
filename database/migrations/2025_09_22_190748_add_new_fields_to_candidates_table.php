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
            $table->boolean('ready_to_relocate')->nullable()->after('current_city');
            $table->string('instagram')->nullable()->after('email');
            $table->string('activity_sphere')->nullable()->after('desired_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn(['ready_to_relocate', 'instagram', 'activity_sphere']);
        });
    }
};
