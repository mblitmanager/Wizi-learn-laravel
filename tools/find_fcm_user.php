<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$user = App\Models\User::whereNotNull('fcm_token')->first();
if ($user) {
    echo json_encode(['id' => $user->id, 'token_preview' => substr($user->fcm_token, 0, 10)]);
} else {
    echo json_encode(['found' => false]);
}
