<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('stagiaires', function (Blueprint $table) {
            $table->foreignId('partenaire_id')->nullable()->constrained('partenaires')->onDelete('set null')->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('stagiaires', function (Blueprint $table) {
            $table->dropForeign(['partenaire_id']);
            $table->dropColumn('partenaire_id');
        });
    }
};
