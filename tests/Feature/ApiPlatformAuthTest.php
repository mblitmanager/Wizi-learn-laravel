<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Les CRUD générés par API Platform (#[ApiResource]) doivent exiger un JWT.
 */
class ApiPlatformAuthTest extends TestCase
{
    use RefreshDatabase;

    private const HEADERS = ['Accept' => 'application/ld+json'];

    public static function protectedEndpoints(): array
    {
        return [
            'liste users' => ['GET', '/api/users'],
            'suppression user' => ['DELETE', '/api/users/1'],
            'liste stagiaires' => ['GET', '/api/stagiaires'],
            'création quiz' => ['POST', '/api/quizzes'],
        ];
    }

    #[DataProvider('protectedEndpoints')]
    public function test_anonymous_requests_are_rejected(string $method, string $uri): void
    {
        $user = User::factory()->create();

        $this->json($method, $uri, [], self::HEADERS)->assertUnauthorized();

        $this->assertModelExists($user);
    }

    public function test_authenticated_requests_are_allowed(): void
    {
        $user = User::factory()->create(['role' => 'administrateur']);
        $token = JWTAuth::fromUser($user);

        $this->json('GET', '/api/users', [], self::HEADERS + ['Authorization' => "Bearer {$token}"])
            ->assertOk();
    }
}
