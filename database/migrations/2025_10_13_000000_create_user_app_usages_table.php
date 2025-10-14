<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_app_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('platform', ['android', 'ios']);
            $table->timestamp('first_used_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('app_version')->nullable();
            $table->string('device_model')->nullable();
            $table->string('os_version')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'platform']);
            $table->index(['platform', 'last_used_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_app_usages');
    }
};
