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
        Schema::table('parainages', function (Blueprint $table) {
            $table->string('token')->nullable()->after('filleul_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parainages', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
};
