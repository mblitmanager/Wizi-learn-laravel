<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quiz_participations', function (Blueprint $table) {
            $table->foreignId('current_question_id')->nullable()->constrained('questions')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('quiz_participations', function (Blueprint $table) {
            $table->dropForeign(['current_question_id']);
            $table->dropColumn('current_question_id');
        });
    }
};
