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
        Schema::create('stagiaires', function (Blueprint $table) {
            $table->id();
            $table->string('civilite')->nullable();
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('role')->default('stagiaire');
            $table->boolean('statut')->default(true);
            $table->foreignId('formation_id')->constrained('formations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('formateur_id')->constrained('formateurs')->onDelete('cascade');
            $table->foreignId('commercial_id')->constrained('commercials')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stagiaires');
    }
};
