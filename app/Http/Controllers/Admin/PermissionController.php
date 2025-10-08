<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::with('roles')
                               ->withCount('roles')
                               ->orderBy('group')
                               ->orderBy('name')
                               ->get()
                               ->groupBy('group');
                               
        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Permission::distinct()->pluck('group')->filter();
        return view('admin.permissions.create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string|max:500',
            'group' => 'required|string|max:100'
        ]);

        Permission::create([
            'name' => $request->name,
            'description' => $request->description,
            'group' => $request->group,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('permissions.index')
                        ->with('success', 'Permission créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles.users');
        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        $groups = Permission::distinct()->pluck('group')->filter();
        return view('admin.permissions.edit', compact('permission', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions')->ignore($permission->id)
            ],
            'description' => 'nullable|string|max:500',
            'group' => 'required|string|max:100'
        ]);

        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
            'group' => $request->group,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('permissions.index')
                        ->with('success', 'Permission mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        if ($permission->roles()->count() > 0) {
            return redirect()->route('permissions.index')
                           ->with('error', 'Impossible de supprimer cette permission car elle est attribuée à des rôles.');
        }

        $permission->delete();

        return redirect()->route('permissions.index')
                        ->with('success', 'Permission supprimée avec succès.');
    }

    /**
     * Toggle permission status
     */
    public function toggleStatus(Permission $permission)
    {
        $permission->update(['is_active' => !$permission->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $permission->is_active,
            'message' => 'Statut de la permission mis à jour'
        ]);
    }
}