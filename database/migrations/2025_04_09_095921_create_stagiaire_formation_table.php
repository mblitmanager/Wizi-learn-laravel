<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('stagiaire_formations', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('stagiaire_id')->constrained('stagiaires')->onDelete('cascade');
    //         $table->foreignId('catalogue_formation_id')->constrained('catalogue_formations')->onDelete('cascade');
    //         $table->softDeletes();
    //         $table->timestamps();
    //     });
    // }
    public function up()
    {
        Schema::create('stagiaire_catalogue_formations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stagiaire_id')->constrained('stagiaires')->onDelete('cascade');
            $table->foreignId('catalogue_formation_id')->constrained('catalogue_formations')->onDelete('cascade');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stagiaire_catalogue_formations');
    }
};
