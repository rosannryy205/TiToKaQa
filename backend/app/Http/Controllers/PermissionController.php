<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function getAllPermission()
    {
        $permission = Permission::all();
        return $permission;
    }

    public function deletePermission(Request $request)
    {
        $permission = Permission::find($request->id);
        $permission->delete();
        return response()->json(['message' => 'Xoá vai trò thành công']);
    }
}
