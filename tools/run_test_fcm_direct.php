<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(App\Services\NotificationService::class);
$user = App\Models\User::find(2);
if (!$user) { echo "No user"; exit(1); }
$sent = $service->sendFcmToUser($user, 'Test direct', 'Payload via direct call', ['type' => 'test']);
echo $sent ? "SENT" : "FAILED";
