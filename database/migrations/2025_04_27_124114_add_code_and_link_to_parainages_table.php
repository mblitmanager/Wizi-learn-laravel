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
            if (!Schema::hasColumn('parainages', 'code')) {
                $table->string('code')->nullable()->after('token');
            }
            if (!Schema::hasColumn('parainages', 'link')) {
                $table->string('link')->nullable()->after('code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parainages', function (Blueprint $table) {
            $table->dropColumn(['code', 'link']);
        });
    }
}; 