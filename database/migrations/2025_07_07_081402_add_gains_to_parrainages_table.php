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
        Schema::table('parrainages', function (Blueprint $table) {
            $table->decimal('gains', 10, 2)->nullable()->after('points');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parrainages', function (Blueprint $table) {
            $table->dropColumn('gains');
        });
    }
};
