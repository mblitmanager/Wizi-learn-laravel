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
            if (!Schema::hasColumn('parainages', 'parrain_id')) {
                $table->unsignedBigInteger('parrain_id')->after('id');
                $table->foreign('parrain_id')->references('id')->on('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('parainages', 'filleul_id')) {
                $table->unsignedBigInteger('filleul_id')->after('parrain_id');
                $table->foreign('filleul_id')->references('id')->on('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('parainages', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('parainages', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parainages', function (Blueprint $table) {
            $table->dropForeign(['parrain_id']);
            $table->dropForeign(['filleul_id']);
            $table->dropColumn(['parrain_id', 'filleul_id', 'created_at', 'updated_at']);
        });
    }
};
