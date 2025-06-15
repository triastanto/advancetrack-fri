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
            $table->foreignId('document_type_id')->constrained('document_types')->onDelete('restrict');
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('state_id')->default(1); // Default to DRAFT state for workflow
            $table->string('semester')->nullable();
            $table->year('year')->nullable();
            $table->date('upload_date')->nullable();
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
