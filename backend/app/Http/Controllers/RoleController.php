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
            'permissions' => 'nullable|array',
        ]);
        $role = Role::find($request->role_id);
        $validPermissions = Permission::whereIn('name', $request->permissions)->pluck('name')->toArray();

        $role->syncPermissions($validPermissions);

        return response()->json(['message' => 'Cập nhật quyền thành công']);
    }

    public function createRoleWithPermissions(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'message' => 'Tạo vai trò và gán quyền thành công',
            'role' => $role,
            'permissions' => $role->permissions->pluck('name'),
        ]);
    }

    public function deleteRole($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'message' => 'Vai trò không tồn tại'
            ], 404);
        }

        $userCount = $role->users()->count();

        if ($userCount > 0) {
            return response()->json([
                'message' => 'Không thể xoá. Có ' . $userCount . ' người dùng đang sử dụng vai trò này.'
            ], 400);
        }

        $role->delete();

        return response()->json(['message' => 'Xoá vai trò thành công']);
    }

}
