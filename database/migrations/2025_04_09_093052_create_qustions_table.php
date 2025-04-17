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
            $table->text('text');
            $table->enum('type', [
                'question audio',
                'remplir le champ vide',
                'carte flash',
                'correspondance',
                'choix multiples',
                'rearrangement',
                'vrai/faux',
                'banque de mots',
            ]);
            $table->string('reponse_correct')->nullable();
            $table->string('explication')->nullable();
            $table->string('points')->nullable();
            $table->string('astuce')->nullable();
            $table->string('media_url', 255)->nullable();
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
