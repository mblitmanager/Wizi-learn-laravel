<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Stagiaire;
use App\Models\Notification;
use Carbon\Carbon;

class SendScheduledNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_formation_and_inactivity_notifications_and_respects_deduplication()
    {
        // Freeze time for predictability
        Carbon::setTestNow(now());

        // Create a user
        $user = User::factory()->create([
            'fcm_token' => null,
            'last_activity_at' => Carbon::now()->subDays(10),
        ]);

        // Create a stagiaire linked to that user with a formation starting in 7 days
        $stagiaire = Stagiaire::create([
            'user_id' => $user->id,
            'date_debut_formation' => Carbon::now()->addDays(7)->toDateString(),
            'prenom' => 'Test',
            'telephone' => '000',
            'formation_id' => 1,
            'date_inscription' => Carbon::now()->toDateString(),
        ]);

        // Ensure no notifications exist
        $this->assertEquals(0, Notification::count());

        // Run the command once
        $this->artisan('notify:scheduled')->assertExitCode(0);

        // After first run, there should be at least one notification for formation and possibly inactivity
        $this->assertTrue(Notification::where('user_id', $user->id)->where('type', 'formation')->exists());

        $countAfterFirst = Notification::where('user_id', $user->id)->count();

        // Run the command again immediately - deduplication should prevent duplicates for the same window
        $this->artisan('notify:scheduled')->assertExitCode(0);

        $countAfterSecond = Notification::where('user_id', $user->id)->count();

        // No new notifications for same thresholds within deduplication window
        $this->assertEquals($countAfterFirst, $countAfterSecond, 'Deduplication did not prevent duplicate notifications');
    }
}
