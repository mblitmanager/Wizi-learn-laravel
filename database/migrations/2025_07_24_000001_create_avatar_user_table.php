<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('avatar_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('avatar_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();

            $table->foreign('avatar_id')->references('id')->on('avatars');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
    public function down()
    {
        Schema::dropIfExists('avatar_user');
    }
}; 