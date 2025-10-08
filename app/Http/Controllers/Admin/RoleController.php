<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('permissions')
                    ->withCount('users')
                    ->orderBy('name')
                    ->get();
                    
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::where('is_active', true)
                               ->orderBy('group')
                               ->orderBy('name')
                               ->get()
                               ->groupBy('group');
                               
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->pluck('name');
                $role->syncPermissions($permissions);
            }
        });

        return redirect()->route('roles.index')
                        ->with('success', 'Rôle créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if ($role->is_protected) {
            return redirect()->route('roles.index')
                           ->with('error', 'Ce rôle est protégé et ne peut pas être modifié.');
        }

        $permissions = Permission::where('is_active', true)
                               ->orderBy('group')
                               ->orderBy('name')
                               ->get()
                               ->groupBy('group');
                               
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if ($role->is_protected) {
            return redirect()->route('roles.index')
                           ->with('error', 'Ce rôle est protégé et ne peut pas être modifié.');
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($role->id)
            ],
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::transaction(function () use ($request, $role) {
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->pluck('name');
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }
        });

        return redirect()->route('roles.index')
                        ->with('success', 'Rôle mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->is_protected) {
            return redirect()->route('roles.index')
                           ->with('error', 'Ce rôle est protégé et ne peut pas être supprimé.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                           ->with('error', 'Impossible de supprimer ce rôle car il est attribué à des utilisateurs.');
        }

        $role->delete();

        return redirect()->route('roles.index')
                        ->with('success', 'Rôle supprimé avec succès.');
    }

    /**
     * Toggle role status
     */
    public function toggleStatus(Role $role)
    {
        if ($role->is_protected) {
            return response()->json(['error' => 'Ce rôle est protégé'], 403);
        }

        $role->update(['is_active' => !$role->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $role->is_active,
            'message' => 'Statut du rôle mis à jour'
        ]);
    }
}