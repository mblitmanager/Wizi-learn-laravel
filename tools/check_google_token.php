<?php
require __DIR__ . '/../vendor/autoload.php';
$servicePath = __DIR__ . '/../storage/app/firebase-service-account.json';
header('Content-Type: application/json');
$out = ['ok' => false];
try {
    if (!file_exists($servicePath)) {
        $out['error'] = 'service file missing';
        echo json_encode($out);
        exit(0);
    }
    $client = new Google_Client();
    $client->setAuthConfig($servicePath);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $token = $client->fetchAccessTokenWithAssertion();
    $out['ok'] = isset($token['access_token']);
    $out['has_error'] = isset($token['error']);
    if (isset($token['access_token'])) $out['token_preview'] = substr($token['access_token'], 0, 10);
    $out['raw'] = $token;
} catch (Throwable $e) {
    $out['exception'] = $e->getMessage();
}
echo json_encode($out, JSON_PRETTY_PRINT);
