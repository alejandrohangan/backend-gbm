<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission as ModelsPermission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::select(['id', 'name'])
            ->with('permissions')
            ->withCount('permissions as total_permissions')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($roles);
    }

    public function getUsersForRole(int $id)
    {
        $role = Role::findOrFail($id);
        $users = User::all();

        $users->each(function ($user) use ($role) {
            $user->has_current_role = $user->hasRole($role->name);
        });

        return response()->json($users);
    }

    public function getAllPermissions()
    {
        $permissions = ModelsPermission::select('id', 'name')->get();
        return response()->json($permissions);
    }

    public function show(int $id)
    {
        $role = Role::with('permissions:id,name')->findOrFail($id);

        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('id')->toArray()
        ]);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:5|max:40',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'integer|exists:permissions,id'
        ]);

        $role = Role::create(['name' => $validated['name']]);

        foreach ($validated['permissions'] as $permissionId) {
            $role->givePermissionTo($permissionId);
        }

        return response()->json(['message' => 'Role created successfully'], 201);
    }

    public function update(int $id, Request $request)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|min:5|max:40',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'integer|exists:permissions,id'
        ]);

        // Actualizar el nombre del rol
        $role->update(['name' => $validated['name']]);

        // Sincronizar permisos - esto reemplaza todos los permisos actuales con los nuevos
        $role->syncPermissions($validated['permissions']);

        return response()->json(['message' => 'Role updated successfully'], 200);
    }

    public function delete(int $id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting role'
            ], 500);
        }
    }

    private function rules() {}
}
