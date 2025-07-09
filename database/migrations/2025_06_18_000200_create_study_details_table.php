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
        Schema::create('study_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_calendar_id')->constrained('study_calendars')->onDelete('cascade');
            $table->string('university_name');
            $table->text('university_address');
            $table->string('university_email');
            $table->string('university_phone');
            $table->foreignId('study_program_id')->constrained('study_programs')->onDelete('cascade');
            $table->text('study_address');
            $table->string('study_level');
            $table->string('scholarship')->nullable();
            $table->string('funding_source')->nullable();
            $table->text('study_regulation_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_details');
    }
};
