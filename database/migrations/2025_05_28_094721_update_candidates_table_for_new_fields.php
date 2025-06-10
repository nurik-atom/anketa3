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
            // Удаляем старые колонки
            $table->dropColumn([
                'surname',
                'name',
                'patronymic',
                'driving_license_type',
                'gallup_pdf',
                'mbti_type'
            ]);

            // Добавляем новые колонки
            $table->string('full_name')->after('user_id');
            $table->boolean('has_driving_license')->default(false)->after('social_media_hours_weekly');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Восстанавливаем старые колонки
            $table->string('surname')->nullable();
            $table->string('name')->nullable();
            $table->string('patronymic')->nullable();
            $table->string('driving_license_type')->nullable();
            $table->string('gallup_pdf')->nullable();
            $table->string('mbti_type')->nullable();

            // Удаляем новые колонки
            $table->dropColumn([
                'full_name',
                'has_driving_license'
            ]);
        });
    }
};
