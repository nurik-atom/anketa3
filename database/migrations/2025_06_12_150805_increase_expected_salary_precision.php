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
            // Увеличиваем размер поля expected_salary для больших зарплат
            // decimal(15, 2) позволит хранить зарплаты до 999,999,999,999.99
            $table->decimal('expected_salary', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Возвращаем к предыдущему размеру
            $table->decimal('expected_salary', 10, 2)->nullable()->change();
        });
    }
};
