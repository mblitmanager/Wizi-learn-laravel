namespace Tests\Feature;

use Tests\TestCase;
use App\Services\QuizService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuizStagiaireControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_questions_by_quiz_id_returns_played_questions_structure()
    {
        // Prepare a fake user object (the controller only reads id and stagiaire->id)
        $user = new \stdClass();
        $user->id = 9999;
        $user->stagiaire = (object) ['id' => 8888];

        // Mock JWTAuth facade to return our fake user
        JWTAuth::shouldReceive('parseToken->authenticate')->andReturn($user);

        // Prepare fake question structure returned by the QuizService
        $q1 = new \stdClass();
        $q1->id = 42;
        $q1->texte = 'Quelle est la couleur du ciel ?';
        $r1 = new \stdClass();
        $r1->id = 420;
        $r1->text = 'Bleu';
        $r1->isCorrect = true;
        $q1->reponses = [$r1];

        $questions = [$q1];

        // Create a simple PHPUnit mock for QuizService
        $quizServiceFake = $this->createMock(QuizService::class);
        $quizServiceFake->method('getQuestionsByQuizId')->willReturn($questions);

        // Bind the fake service into the container so controller uses it
        $this->app->instance(QuizService::class, $quizServiceFake);

        // Call the API route (we sent JWTAuth mock earlier)
        $response = $this->getJson('/api/quiz/99/questions');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'playedQuestions']);
    }
}
    }
}
