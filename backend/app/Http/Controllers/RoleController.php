<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function userProfile(Request $request)
    {
        $user = User::find($request->id);

        return response()->json([
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    public function getAllRole()
    {
        $roles = Role::all();

        foreach ($roles as $role) {
            $role->display_name = ucfirst($role->name);
        }
        return $roles;
    }

    public function getAllPermission(Request $request)
    {
        $role = Role::with('permissions')->find($request->id);
        $abilities = [];

        foreach ($role->permissions as $permission) {
            [$action, $module] = explode('_', $permission->name);
            $abilities[$module][$action] = true;
        }

        return response()->json([
            'data' => [
                'abilities' => $abilities,
            ]
        ]);
    }

    public function updatePermission(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
        ]);
        $role = Role::find($request->role_id);
        $validPermissions = Permission::whereIn('name', $request->permissions)->pluck('name')->toArray();

        $role->syncPermissions($validPermissions);

        return response()->json(['message' => 'Cập nhật quyền thành công']);
    }
}
