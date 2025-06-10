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
            // Basic Information
            $table->string('surname')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->string('patronymic')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('gender')->nullable()->change();
            $table->string('marital_status')->nullable()->change();
            $table->date('birth_date')->nullable()->change();
            $table->string('birth_place')->nullable()->change();
            $table->string('current_city')->nullable()->change();
            $table->string('photo')->nullable()->change();

            // Additional Information
            $table->string('religion')->nullable()->change();
            $table->boolean('is_practicing')->nullable()->change();
            $table->json('family_members')->nullable()->change();
            $table->text('hobbies')->nullable()->change();
            $table->text('interests')->nullable()->change();
            $table->json('visited_countries')->nullable()->change();
            $table->integer('books_per_year')->nullable()->change();
            $table->json('favorite_sports')->nullable()->change();
            $table->integer('entertainment_hours_weekly')->nullable()->change();
            $table->integer('educational_hours_weekly')->nullable()->change();
            $table->integer('social_media_hours_weekly')->nullable()->change();
            $table->string('driving_license_type')->nullable()->change();

            // Education and Work
            $table->string('school')->nullable()->change();
            $table->json('universities')->nullable()->change();
            $table->json('language_skills')->nullable()->change();
            $table->json('computer_skills')->nullable()->change();
            $table->json('work_experience')->nullable()->change();
            $table->integer('total_experience_years')->nullable()->change();
            $table->integer('job_satisfaction')->nullable()->change();
            $table->string('desired_position')->nullable()->change();
            $table->decimal('expected_salary', 10, 2)->nullable()->change();
            $table->text('employer_requirements')->nullable()->change();

            // Assessments
            $table->string('gallup_pdf')->nullable()->change();
            $table->string('mbti_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Basic Information
            $table->string('surname')->nullable(false)->change();
            $table->string('name')->nullable(false)->change();
            $table->string('patronymic')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('gender')->nullable(false)->change();
            $table->string('marital_status')->nullable(false)->change();
            $table->date('birth_date')->nullable(false)->change();
            $table->string('birth_place')->nullable(false)->change();
            $table->string('current_city')->nullable(false)->change();
            $table->string('photo')->nullable(false)->change();

            // Additional Information
            $table->string('religion')->nullable(false)->change();
            $table->boolean('is_practicing')->nullable(false)->change();
            $table->json('family_members')->nullable(false)->change();
            $table->text('hobbies')->nullable(false)->change();
            $table->text('interests')->nullable(false)->change();
            $table->json('visited_countries')->nullable(false)->change();
            $table->integer('books_per_year')->nullable(false)->change();
            $table->json('favorite_sports')->nullable(false)->change();
            $table->integer('entertainment_hours_weekly')->nullable(false)->change();
            $table->integer('educational_hours_weekly')->nullable(false)->change();
            $table->integer('social_media_hours_weekly')->nullable(false)->change();
            $table->string('driving_license_type')->nullable(false)->change();

            // Education and Work
            $table->string('school')->nullable(false)->change();
            $table->json('universities')->nullable(false)->change();
            $table->json('language_skills')->nullable(false)->change();
            $table->json('computer_skills')->nullable(false)->change();
            $table->json('work_experience')->nullable(false)->change();
            $table->integer('total_experience_years')->nullable(false)->change();
            $table->integer('job_satisfaction')->nullable(false)->change();
            $table->string('desired_position')->nullable(false)->change();
            $table->decimal('expected_salary', 10, 2)->nullable(false)->change();
            $table->text('employer_requirements')->nullable(false)->change();

            // Assessments
            $table->string('gallup_pdf')->nullable(false)->change();
            $table->string('mbti_type')->nullable(false)->change();
        });
    }
};
