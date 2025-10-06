<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('group')->nullable()->after('description');
            $table->boolean('is_active')->default(true)->after('group');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->boolean('is_active')->default(true)->after('description');
            $table->boolean('is_protected')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
   public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['description', 'group', 'is_active']);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['description', 'is_active', 'is_protected']);
        });
    }
};
