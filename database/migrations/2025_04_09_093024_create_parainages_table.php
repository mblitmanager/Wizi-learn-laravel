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
        Schema::create('parainages', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_filleul')->nullable();
            // $table->foreignId('stagiaire_id')->constrained('stagiaires')->onDelete('cascade');
            $table->string('lien')->nullable();
            $table->string('points')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parainages');
    }
};
