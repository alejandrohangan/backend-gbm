<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::with('permissions')
            ->withCount('permissions as total_permissions')
            ->orderBy('id', 'desc')
            ->get();;

        return response()->json($roles);
    }

    public function create(Request $request)
    {

        $role = Role::create(['name' => $request->roleName]);
        $permissions = $request->permissions;

        foreach ($permissions as $permission) {
            $role->givePermissionTo($permission);
        }
    }

    public function delete(int $id)
    {
        $role = Role::findOrFail($id);

        Role::destroy($role);
    }

    private function rules() {}
}
