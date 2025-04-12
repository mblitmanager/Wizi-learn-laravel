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
        Schema::table('questions', function (Blueprint $table) {
            $table->text('text')->nullable()->after('quiz_id'); // Le contenu textuel ou consigne
            $table->string('media_url', 255)->nullable()->after('text'); // Pour les questions audio/vidéos
            $table->enum('type', [
                'audioquestion',
                'fillblank',
                'flashcard',
                'matching',
                'multiplechoice',
                'ordering',
                'truefalse',
                'wordbank',
            ])->change(); // Mise à jour du type ENUM
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('reponse_correct');
            $table->dropColumn('immage_illustration');
        });
    }
};
