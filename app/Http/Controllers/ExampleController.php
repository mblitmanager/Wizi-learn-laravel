<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function sendNotification(Request $request)
    {
        $userId = $request->user()->id;
        $message = "Nouvelle notification de test !";

        event(new NotificationEvent($message, $userId));

        return response()->json(['message' => 'Notification envoyée']);
    }
}
