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
        Schema::create('progressions', function (Blueprint $table) {
            $table->id();
            $table->boolean('termine')->nullable();
            $table->string('points')->nullable();
            $table->foreignId('stagiaire_id')->constrained()->onDelete('cascade')->nullable();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade')->nullable();
            $table->foreignId('formation_id')->constrained()->onDelete('cascade')->nullable();
            $table->integer('pourcentage')->nullable();
            $table->text('explication')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progressions');
    }
};
