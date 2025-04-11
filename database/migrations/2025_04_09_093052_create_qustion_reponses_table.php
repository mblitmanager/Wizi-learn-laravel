<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->string('question')->nullable();
            $table->string('reponse')->nullable();
            $table->string('type')->nullable();
            $table->string('reponse_correct')->nullable();
            $table->string('immage_illustration')->nullable();
            $table->string('explication')->nullable();
            $table->string('points')->nullable();
            $table->string('astuce')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
