<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('google_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('google_calendar_id')->constrained('google_calendars')->onDelete('cascade');
            $table->string('google_id')->unique();
            $table->string('summary')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('html_link')->nullable();
            $table->string('hangout_link')->nullable();
            $table->json('organizer')->nullable();
            $table->json('attendees')->nullable();
            $table->string('status')->nullable();
            $table->json('recurrence')->nullable();
            $table->string('event_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_calendar_events');
    }
};
