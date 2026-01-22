<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_stagiaire', function (Blueprint $table) {
            // Ajouter les colonnes de tracking vidéo
            $table->integer('current_time')->default(0)->comment('Position actuelle de lecture en secondes');
            $table->integer('duration')->default(0)->comment('Durée totale de la vidéo en secondes');
            $table->float('percentage')->default(0)->comment('Pourcentage de complétion (0-100)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media_stagiaire', function (Blueprint $table) {
            $table->dropColumn(['current_time', 'duration', 'percentage']);
        });
    }
};
