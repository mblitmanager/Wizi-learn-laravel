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
        Schema::table('media', function (Blueprint $table) {
            $table->enum('categorie', ['tutoriel', 'astuce'])->nullable()->after('type');
            $table->integer('duree')->nullable()->after('categorie');
            $table->integer('ordre')->nullable()->after('duree');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['categorie', 'duree', 'ordre']);
        });
    }
}; 