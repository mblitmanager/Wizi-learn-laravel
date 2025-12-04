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
        Schema::create('quiz_statistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedBigInteger('total_attempts')->default(0);
            $table->float('average_score')->default(0);
            $table->float('average_time')->default(0);
            $table->float('success_rate')->default(0);
            $table->json('score_distribution')->nullable();
            $table->json('hardest_questions')->nullable();
            $table->json('easiest_questions')->nullable();
            $table->json('trends_over_time')->nullable();
            $table->timestamps();

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_statistics');
    }
};
