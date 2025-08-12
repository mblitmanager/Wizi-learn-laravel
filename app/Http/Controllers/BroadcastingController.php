<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class BroadcastingController extends Controller
{
    public function auth(Request $request)
    {
        return Broadcast::auth($request);
    }
}
