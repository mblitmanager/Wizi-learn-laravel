<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Get list of all users (for email selection)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = User::select('id', 'name', 'email', 'role');

        // Optional role filter
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Optional search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Pagination (optional)
        if ($request->has('perPage')) {
            $users = $query->paginate($request->perPage);
        } else {
            $users = $query->get();
        }

        return response()->json($users);
    }
}
