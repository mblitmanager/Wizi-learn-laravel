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
        Schema::table('progressions', function (Blueprint $table) {
            $table->integer('score')->nullable();
            $table->integer('correct_answers')->nullable();
            $table->integer('total_questions')->nullable();
            $table->integer('time_spent')->nullable()->comment('Temps passé en secondes');
            $table->timestamp('completion_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progressions', function (Blueprint $table) {
            $table->dropColumn([
                'score',
                'correct_answers',
                'total_questions',
                'time_spent',
                'completion_time'
            ]);
        });
    }
};
