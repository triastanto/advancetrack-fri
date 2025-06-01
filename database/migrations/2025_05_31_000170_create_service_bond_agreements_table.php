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
        Schema::create('service_bond_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->date('upload_date');
            $table->enum('verification_status', ['draft', 'pending', 'verified', 'rejected'])->default('draft');
            $table->text('verification_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_bond_agreements');
    }
};
