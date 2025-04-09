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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('titre')->nullable();
            $table->string('description')->nullable();
            $table->string('date_debut')->nullable();
            $table->string('date_fin')->nullable();
            $table->string('evenement')->nullable();
            $table->string('commentaire')->nullable();
            $table->foreignId('stagiaire_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
