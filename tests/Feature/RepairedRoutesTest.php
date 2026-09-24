<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Parrainage;
use App\Models\Quiz;
use App\Models\Stagiaire;
use App\Models\User;
use Database\Seeders\CatalogueFormationSeeder;
use Database\Seeders\FormationSeeder;
use Database\Seeders\MediaSeeder;
use Database\Seeders\ParrainageSeeder;
use Database\Seeders\QuizSeeder;
use Database\Seeders\StagiaireSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Routes qui pointaient vers des méthodes inexistantes (erreur 500).
 */
class RepairedRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class,
            FormationSeeder::class,
            CatalogueFormationSeeder::class,
            StagiaireSeeder::class,
            QuizSeeder::class,
        ]);
    }

    private function apiAs(User $user): self
    {
        return $this->withHeader('Authorization', 'Bearer '.JWTAuth::fromUser($user));
    }

    public function test_update_progress_saves_position_and_marks_watched_at_90_percent(): void
    {
        $this->seed(MediaSeeder::class);
        $stagiaire = Stagiaire::orderByDesc('id')->first();
        $media = Media::whereDoesntHave('stagiaires', fn ($q) => $q->whereKey($stagiaire->id))->first();

        $this->apiAs($stagiaire->user)
            ->postJson('/api/medias/updateProgress', ['media_id' => $media->id, 'current_time' => 30, 'duration' => 100])
            ->assertOk()
            ->assertJson(['percentage' => 30, 'is_watched' => false]);

        $this->apiAs($stagiaire->user)
            ->postJson('/api/medias/updateProgress', ['media_id' => $media->id, 'current_time' => 95, 'duration' => 100])
            ->assertOk()
            ->assertJson(['is_watched' => true]);

        $pivot = DB::table('media_stagiaire')->where(['media_id' => $media->id, 'stagiaire_id' => $stagiaire->id])->first();
        $this->assertSame(95, (int) $pivot->current_time);
        $this->assertNotNull($pivot->watched_at);
    }

    public function test_update_progress_validates_payload(): void
    {
        $stagiaire = Stagiaire::first();

        $this->apiAs($stagiaire->user)
            ->postJson('/api/medias/updateProgress', ['media_id' => 999999, 'current_time' => -1])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['media_id', 'current_time', 'duration']);
    }

    public function test_filleuls_lists_only_the_parrain_referrals(): void
    {
        $this->seed(ParrainageSeeder::class);
        $parrain = User::where('email', 'stagiaire1@wizi-learn.com')->first();

        $this->apiAs($parrain)
            ->getJson('/api/stagiaire/parrainage/filleuls')
            ->assertOk()
            ->assertJsonCount(Parrainage::where('parrain_id', $parrain->id)->count(), 'data')
            ->assertJsonPath('data.0.email', 'filleul1@wizi-learn.com');
    }

    public function test_admin_can_soft_delete_a_stagiaire(): void
    {
        $admin = User::where('role', 'administrateur')->first();
        $stagiaire = Stagiaire::first();

        $this->actingAs($admin)
            ->delete(route('stagiaires.destroy', $stagiaire->id))
            ->assertRedirect(route('stagiaires.index'));

        $this->assertSoftDeleted($stagiaire);
    }

    public function test_admin_can_export_a_quiz_as_json(): void
    {
        $admin = User::where('role', 'administrateur')->first();
        $quiz = Quiz::first();

        $response = $this->actingAs($admin)->get(route('quiz.export', $quiz->id))->assertOk();

        $payload = json_decode($response->streamedContent(), true);
        $this->assertSame($quiz->titre, $payload['quiz']['titre']);
        $this->assertCount($quiz->questions()->count(), $payload['questions']);
        $this->assertArrayHasKey('is_correct', $payload['questions'][0]['reponses'][0]);
    }
}
