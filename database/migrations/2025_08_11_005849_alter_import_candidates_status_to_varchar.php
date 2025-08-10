<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('varchar', function (Blueprint $table) {
            DB::statement("ALTER TABLE `import_candidates` MODIFY `status` VARCHAR(32) NOT NULL DEFAULT 'new'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('varchar', function (Blueprint $table) {
            DB::statement("ALTER TABLE `import_candidates` MODIFY `status` ENUM('new','processed','failed') NOT NULL DEFAULT 'new'");
        });
    }
};
