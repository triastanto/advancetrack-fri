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
            $table->string('nidn')->unique(); // NIDN (Nomor Induk Dosen Nasional)
            $table->string('position');
            $table->enum('role', [
                'lecturer',
                'hr_finance_staff',
                'head_of_hr_finance',
                'fri_vice_dean',
                'head_of_study_program',
                'head_of_research_group'
            ]);
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('functional_position')->nullable();
            $table->text('origin_address')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('photo')->nullable();
            $table->foreignId('research_lab_id')->nullable()->constrained('research_labs')->onDelete('set null');
            $table->boolean('is_lab_head')->default(false);
            $table->boolean('is_approved')->default(false); // HR/Finance approval required
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_on')->nullable();
            $table->timestamps();

            // Add indexes for better query performance
            $table->index('role');
            $table->index('nidn');
            $table->index('research_lab_id');
            $table->index('is_lab_head');
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
