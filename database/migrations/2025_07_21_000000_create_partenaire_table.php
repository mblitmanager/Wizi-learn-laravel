<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('partenaires', function (Blueprint $table) {
            $table->id();
            $table->string('identifiant')->unique();
            $table->string('adresse');
            $table->string('ville');
            $table->string('departement');
            $table->string('code_postal');
            $table->string('type');
            $table->timestamps();
        });
        Schema::create('partenaire_stagiaire', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partenaire_id')->constrained('partenaires')->onDelete('cascade');
            $table->foreignId('stagiaire_id')->constrained('stagiaires')->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('partenaire_stagiaire');
        Schema::dropIfExists('partenaires');
    }
};
