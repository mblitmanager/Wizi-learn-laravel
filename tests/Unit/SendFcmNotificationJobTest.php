<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Jobs\SendFcmNotificationJob;
use App\Services\NotificationService;

class SendFcmNotificationJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_calls_notification_service()
    {
        $user = (object)['id' => 1, 'fcm_token' => 'token123'];
        $title = 'Titre test';
        $body = 'Corps test';
        $data = ['a' => 'b'];

        $mock = $this->createMock(NotificationService::class);
        $mock->expects($this->once())
             ->method('sendFcmToUser')
             ->with($user, $title, $body, $data)
             ->willReturn(true);

        $this->app->instance(NotificationService::class, $mock);

        $job = new SendFcmNotificationJob($user, $title, $body, $data);

        $job->handle($mock);
        $this->assertTrue(true); // reached
    }
}
