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
            $table->text('text')->nullable(); // Texte de la réponse ou élément à associer
            $table->boolean('is_correct')->nullable(); // Pour multiplechoice, truefalse, etc.
            $table->integer('position')->nullable(); // Pour ordering
            $table->string('match_pair')->nullable(); // Pour matching (clé à associer)
            $table->string('bank_group')->nullable(); // Pour wordbank ou fillblank (groupe ou tag)
            $table->text('flashcard_back')->nullable(); // Pour flashcards (le verso)
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade'); // Clé étrangère vers la table questions
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
