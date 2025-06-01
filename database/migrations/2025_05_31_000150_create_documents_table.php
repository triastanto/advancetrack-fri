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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->enum('document_type', ['personal_data', 'requirement', 'semester_report', 'final_report', 'graduation', 'additional', 'minutes', 'nde', 'pid']);
            $table->string('file_name');
            $table->string('file_path');
            $table->enum('verification_status', ['draft', 'pending', 'verified', 'rejected'])->default('draft');
            $table->text('verification_note')->nullable();
            $table->timestamp('uploaded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
