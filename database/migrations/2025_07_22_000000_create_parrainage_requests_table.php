<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('parrainage_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parrain_id');
            $table->unsignedBigInteger('filleul_id')->nullable();
            $table->unsignedBigInteger('catalogue_formation_id');
            $table->enum('status', ['pending', 'accepted', 'refused'])->default('pending');
            $table->timestamps();

            $table->foreign('parrain_id')->references('id')->on('stagiaires');
            $table->foreign('filleul_id')->references('id')->on('stagiaires');
            $table->foreign('catalogue_formation_id')->references('id')->on('catalogue_formations');
        });
    }
    public function down()
    {
        Schema::dropIfExists('parrainage_requests');
    }
}; 