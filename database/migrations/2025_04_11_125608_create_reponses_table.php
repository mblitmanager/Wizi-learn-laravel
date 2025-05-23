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
        Schema::create('reponses', function (Blueprint $table) {
            $table->id();
            $table->text('text')->nullable();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->boolean('is_correct')->nullable();
            $table->integer('position')->nullable();
            $table->string('match_pair')->nullable();
            $table->string('bank_group')->nullable();
            $table->text('flashcard_back')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reponses');
    }
};
