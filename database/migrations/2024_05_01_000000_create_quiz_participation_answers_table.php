<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('quiz_participation_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participation_id')->constrained('quiz_participations')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->json('answer_ids');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quiz_participation_answers');
    }
}; 