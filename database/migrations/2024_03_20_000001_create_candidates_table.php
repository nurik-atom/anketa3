<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('surname');
            $table->string('name');
            $table->string('patronymic')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('gender');
            $table->string('marital_status');
            $table->date('birth_date');
            $table->string('birth_place');
            $table->string('current_city');
            $table->string('photo')->nullable();

            // Additional Information
            $table->string('religion')->nullable();
            $table->boolean('is_practicing')->nullable();
            $table->json('family_members')->nullable();
            $table->text('hobbies')->nullable();
            $table->text('interests')->nullable();
            $table->json('visited_countries')->nullable();
            $table->integer('books_per_year')->nullable();
            $table->json('favorite_sports')->nullable();
            $table->integer('entertainment_hours_weekly')->nullable();
            $table->integer('educational_hours_weekly')->nullable();
            $table->integer('social_media_hours_weekly')->nullable();
            $table->string('driving_license_type')->nullable();

            // Education and Work
            $table->string('school');
            $table->json('universities')->nullable();
            $table->json('language_skills')->nullable();
            $table->json('computer_skills')->nullable();
            $table->json('work_experience')->nullable();
            $table->integer('total_experience_years');
            $table->integer('job_satisfaction')->nullable();
            $table->string('desired_position');
            $table->decimal('expected_salary', 12, 2);
            $table->text('employer_requirements')->nullable();

            // Assessments
            $table->string('gallup_pdf')->nullable();
            $table->string('mbti_type')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
}; 