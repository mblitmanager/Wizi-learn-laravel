<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Using raw statement for modifying ENUM in MySQL as it is the most reliable method without extra dependencies
        DB::statement("ALTER TABLE announcements MODIFY COLUMN target_audience ENUM('all', 'stagiaires', 'formateurs', 'autres', 'specific_users') NOT NULL DEFAULT 'all'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE announcements MODIFY COLUMN target_audience ENUM('all', 'creators', 'subscribers') NOT NULL DEFAULT 'all'");
    }
};
