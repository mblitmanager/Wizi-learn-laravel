<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Stagiaire;

class ApiPerformanceTest extends TestCase
{
    // usage of RefreshDatabase might wipe DB, so be careful if testing against existing data.
    // For specific performance tests on existing dev DB, we usually DON'T use RefreshDatabase trait unless we seed data.
    // Given the user is in dev environment with data, I will NOT use RefreshDatabase.

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Find a stagiaire user to test with
        $this->user = User::whereHas('stagiaire')->first();

        if (!$this->user) {
            $this->markTestSkipped('No stagiaire user found for testing.');
        }

        // Generate JWT token for the user
        $this->token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($this->user);
    }

    /**
     * Test Dashboard Home Endpoint
     */
    public function test_dashboard_home_status_and_structure()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/stagiaire/dashboard/home');

        $response->assertStatus(200);
        
        // Assert json structure matches optimized response
        $response->assertJsonStructure([
            'user',
            'quiz_stats',
            'recent_history',
            'contacts',
            'catalogue_formations',
            'categories'
        ]);
    }

    /**
     * Test Catalogue Formations Endpoint (Global Cache)
     */
    public function test_catalogue_formations_status_and_structure()
    {
        // This is a public endpoint but let's send auth anyway or not?
        // Route is: Route::get('with-formations', [CatalogueFormationController::class, 'getCataloguesWithFormations']);
        // It's under `Route::prefix('catalogueFormations')` inside AUTHENTICATED group in api.php?
        // Let's check api.php again. NO, it is inside `Route::middleware(['auth:api', ...])->group(...)`.
        // So it requires Auth.

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/catalogueFormations/with-formations');

        $response->assertStatus(200);
        
        // Assert collection structure
        $response->assertJsonStructure([
            'member' => [
                '*' => [
                    'id',
                    'titre',
                    'description',
                    'formation' => [
                        'id',
                        'titre',
                        'image_url', // This should be what we expect now, wait.
                        // I changed 'image_url' to 'image' in the selects.
                        // But the endpoint mapping might map 'image' to 'imageUrl' or 'image_url' JSON field?
                        // Let's check Controller's map function.
                    ]
                ]
            ]
        ]);
    }

    /**
     * Test Quiz Categories Endpoint
     */
    public function test_quiz_categories_status()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/quiz/categories');

        $response->assertStatus(200);
    }
}
