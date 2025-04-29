<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAcceptedAtToParainagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parainages', function (Blueprint $table) {
            $table->timestamp('accepted_at')->nullable()->after('filleul_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parainages', function (Blueprint $table) {
            $table->dropColumn('accepted_at');
        });
    }
}