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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('employee_number')->unique();
            $table->string('position');
            $table->enum('role', [
                'lecturer',
                'hr_finance_staff', 
                'head_of_hr_finance',
                'fri_vice_dean',
                'head_of_study_program',
                'head_of_research_group'
            ]);
            $table->timestamps();
            
            // Add indexes for better query performance
            $table->index('role');
            $table->index('employee_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
