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
        Schema::create('demande_inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parrain_id')->constrained('users');
            $table->foreignId('filleul_id')->constrained('users');
            $table->foreignId('formation_id')->constrained('catalogue_formations');
            $table->string('statut')->default('en_attente');
            $table->text('donnees_formulaire')->nullable();
            $table->string('lien_parrainage')->nullable();
            $table->string('motif');
            $table->dateTime('date_demande');
            $table->dateTime('date_inscription');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_inscriptions');
    }
};
