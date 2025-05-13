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
        Schema::table('correspondance_pairs', function (Blueprint $table) {
            $table->unsignedBigInteger('left_id')->nullable();
            $table->unsignedBigInteger('right_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('left_id');
            $table->dropColumn('right_id');
        });
    }
};
