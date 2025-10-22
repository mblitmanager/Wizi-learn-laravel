<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetectClientPlatformTest extends TestCase
{
    use RefreshDatabase;

    public function test_detects_client_and_creates_session()
    {
        // Create a user
        $user = User::factory()->create();

        // Issue a JWT token for the user (app uses tymon/jwt-auth)
    $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'X-Client-Type' => 'android',
            'X-Device-Id' => 'device-123',
            'X-App-Version' => '1.2.3',
        ])->getJson('/api/user');

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'last_client' => 'android',
        ]);

        $this->assertDatabaseHas('user_client_sessions', [
            'user_id' => $user->id,
            'device_id' => 'device-123',
            'platform' => 'android',
            'app_version' => '1.2.3',
        ]);
    }
}
