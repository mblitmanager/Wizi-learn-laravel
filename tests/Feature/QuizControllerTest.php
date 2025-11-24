<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Stagiaire;
use App\Models\Quiz;
use App\Models\Participation;
use App\Models\Questions;
use App\Models\Formation;

class QuizControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_resume_participation_returns_correct_data()
    {
        // Create User
        $user = User::factory()->create();

        // Create Stagiaire
        $stagiaire = Stagiaire::create([
            'user_id' => $user->id,
            'prenom' => 'Test',
            'role' => 'stagiaire'
        ]);

        // Create Formation
        $formation = Formation::create([
            'titre' => 'Test Formation',
            'categorie' => 'Test Category',
            'statut' => true
        ]);

        // Create Quiz
        $quiz = Quiz::create([
            'formation_id' => $formation->id,
            'titre' => 'Test Quiz',
            'status' => 'actif',
            'niveau' => 'débutant'
        ]);

        // Create Question
        $question = Questions::create([
            'quiz_id' => $quiz->id,
            'text' => 'Test Question',
            'type' => 'choix multiples'
        ]);

        // Create Participation
        $participation = Participation::create([
            'stagiaire_id' => $stagiaire->id,
            'quiz_id' => $quiz->id,
            'current_question_id' => $question->id,
            'score' => 10,
            'heure' => '00:10:00'
        ]);

        // Authenticate
        $this->actingAs($user, 'api');

        // Call endpoint
        $response = $this->getJson("/api/quiz/{$quiz->id}/participation/resume");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'participation_id' => $participation->id,
                'quiz_id' => $quiz->id,
                'current_question_id' => (string)$question->id, // Cast to string as JSON often returns strings for IDs
                'score' => 10,
                'time_spent' => '00:10:00'
            ]);
    }

    public function test_resume_participation_returns_404_if_no_participation()
    {
        $user = User::factory()->create();
        $stagiaire = Stagiaire::create(['user_id' => $user->id]);
        $formation = Formation::create(['titre' => 'Test']);
        $quiz = Quiz::create(['formation_id' => $formation->id, 'titre' => 'Test Quiz']);

        $this->actingAs($user, 'api');

        $response = $this->getJson("/api/quiz/{$quiz->id}/participation/resume");

        $response->assertStatus(404);
    }
}
