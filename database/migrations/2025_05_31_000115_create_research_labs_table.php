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
        Schema::create('research_labs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('alias_name')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('research_group_id')->constrained('research_groups')->onDelete('cascade');
            $table->timestamps();

            // Add indexes for better query performance
            $table->index('name');
            $table->index('research_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_labs');
    }
};
