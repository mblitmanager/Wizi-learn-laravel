<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$u = App\Models\User::find(2);
if (!$u) { echo json_encode(['found'=>false]); exit; }
$token = $u->fcm_token ?? '';
echo json_encode(['id'=>$u->id, 'present'=>!empty($token), 'len'=>strlen($token), 'preview'=>substr($token,0,40)]);
