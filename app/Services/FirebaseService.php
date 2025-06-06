<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $firebase;
    protected $messaging;

    public function __construct()
    {
        $this->firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'))
            ->withDatabaseUri(config('firebase.database_url'))
            ->createDatabase();

        $this->messaging = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'))
            ->createMessaging();
    }

    public function sendNotification($token, $title, $body, $data = [])
    {
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        return $this->messaging->send($message);
    }

    public function getDatabase()
    {
        return $this->firebase;
    }

    public function getMessaging()
    {
        return $this->messaging;
    }
}
