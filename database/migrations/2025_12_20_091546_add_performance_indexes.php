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
        Schema::table('quizzes', function (Blueprint $table) {
            $table->index('status');
        });
        
        Schema::table('formations', function (Blueprint $table) {
            $table->index('categorie');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('formations', function (Blueprint $table) {
            $table->dropIndex(['categorie']);
        });
    }
};
