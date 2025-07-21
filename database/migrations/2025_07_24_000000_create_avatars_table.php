<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('avatars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->string('unlock_condition')->nullable();
            $table->integer('price_points')->default(0);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('avatars');
    }
}; 