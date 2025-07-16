<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAchievementsTables extends Migration
{
    public function up()
    {
        // Table des succès
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // connexion_serie, points_total, palier, quiz
            $table->integer('condition')->nullable();
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('level')->nullable(); // bronze, silver, gold, etc.
            $table->unsignedBigInteger('quiz_id')->nullable(); // Lien vers un quiz spécifique
            $table->timestamps();
        });

        // Table pivot stagiaire_achievements
        Schema::create('stagiaire_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stagiaire_id');
            $table->unsignedBigInteger('achievement_id');
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();

            $table->foreign('stagiaire_id')->references('id')->on('stagiaires')->onDelete('cascade');
            $table->foreign('achievement_id')->references('id')->on('achievements')->onDelete('cascade');
            $table->unique(['stagiaire_id', 'achievement_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stagiaire_achievements');
        Schema::dropIfExists('achievements');
    }
}
