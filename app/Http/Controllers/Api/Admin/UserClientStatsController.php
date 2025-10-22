<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserClientStatsController extends Controller
{
    /**
     * Return counts of users grouped by last_client.
     */
    public function index(Request $request)
    {
        $counts = User::query()
            ->selectRaw("COALESCE(last_client, 'unknown') as client, COUNT(*) as total")
            ->groupBy('client')
            ->pluck('total', 'client');

        return response()->json([
            'data' => $counts,
        ]);
    }
}
