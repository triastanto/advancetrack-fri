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
        Schema::create('workflow_histories', function (Blueprint $table) {
            $table->id();
            $table->string('workflowable_type');
            $table->unsignedBigInteger('workflowable_id');
            $table->string('workflow_name');
            $table->unsignedBigInteger('from_state')->nullable(); // Allow null for initial state
            $table->unsignedBigInteger('to_state');
            $table->unsignedBigInteger('transition')->nullable(); // Allow null for initialization
            $table->json('context')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->index(['workflowable_type', 'workflowable_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_histories');
    }
};
