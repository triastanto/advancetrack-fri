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
        Schema::create('employee_study_program', function (Blueprint $table) {
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('study_program_id')->constrained('study_programs')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['employee_id', 'study_program_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_study_program');
    }
};
