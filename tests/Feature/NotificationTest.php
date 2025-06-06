<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Media;
use App\Models\CatalogueFormation;
use App\Notifications\QuizCreated;
use App\Notifications\MediaCreated;
use App\Notifications\NewFilleul;
use App\Notifications\CatalogueFormationUpdated;
use App\Notifications\ClassementReset;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_quiz_created_notification()
    {
        Notification::fake();

        $quiz = Quiz::factory()->create();
        $users = User::factory()->count(3)->create(['role' => 'stagiaire']);

        $this->actingAs($users[0])
            ->postJson('/api/notify-quiz-created', ['quiz_id' => $quiz->id]);

        Notification::assertSentTo(
            $users,
            QuizCreated::class,
            function ($notification, $channels) use ($quiz) {
                return $notification->quiz->id === $quiz->id;
            }
        );
    }

    public function test_media_created_notification()
    {
        Notification::fake();

        $media = Media::factory()->create();
        $users = User::factory()->count(3)->create(['role' => 'stagiaire']);

        $this->actingAs($users[0])
            ->postJson('/api/notify-media-created', ['media_id' => $media->id]);

        Notification::assertSentTo(
            $users,
            MediaCreated::class,
            function ($notification, $channels) use ($media) {
                return $notification->media->id === $media->id;
            }
        );
    }

    public function test_new_filleul_notification()
    {
        Notification::fake();

        $parrain = User::factory()->create(['role' => 'stagiaire']);
        $filleul = User::factory()->create(['role' => 'stagiaire']);

        $this->actingAs($parrain)
            ->postJson('/api/notify-new-filleul', [
                'filleul_id' => $filleul->id,
                'parrain_id' => $parrain->id
            ]);

        Notification::assertSentTo(
            $parrain,
            NewFilleul::class,
            function ($notification, $channels) use ($filleul) {
                return $notification->filleul->id === $filleul->id;
            }
        );
    }

    public function test_catalogue_formation_updated_notification()
    {
        Notification::fake();

        $formation = CatalogueFormation::factory()->create();
        $users = User::factory()->count(3)->create(['role' => 'stagiaire']);

        $this->actingAs($users[0])
            ->postJson('/api/notify-catalogue-formation-updated', ['formation_id' => $formation->id]);

        Notification::assertSentTo(
            $users,
            CatalogueFormationUpdated::class,
            function ($notification, $channels) use ($formation) {
                return $notification->formation->id === $formation->id;
            }
        );
    }

    public function test_classement_reset_notification()
    {
        Notification::fake();

        $users = User::factory()->count(3)->create(['role' => 'stagiaire']);

        $this->actingAs($users[0])
            ->postJson('/api/notify-classement-reset');

        Notification::assertSentTo(
            $users,
            ClassementReset::class
        );
    }
}
