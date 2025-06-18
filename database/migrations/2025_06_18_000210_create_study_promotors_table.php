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
        Schema::create('study_promotors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_detail_id')->constrained('study_details')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_promotors');
    }
};
