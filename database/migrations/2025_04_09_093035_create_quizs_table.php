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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('titre')->nullable();
            $table->string('description')->nullable();
            $table->string('duree')->nullable();
            $table->string('niveau')->nullable();
            $table->string('nb_points_total')->nullable();
            $table->foreignId('formation_id')->constrained('formations')->onDelete('cascade');
            $table->enum('status', ['actif', 'inactif'])->default('inactif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
