<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermission extends Seeder
{
    public function run(): void
    {
        $modules = [
            'dashboard' => 'Thống kê',
            'category' => 'Danh mục',
            'food' => 'Món ăn',
            'topping' => 'Topping',
            'combo' => 'Combo',
            'order' => 'Đơn hàng',
            'table' => 'Bàn',
            'booking' => 'Lịch đặt bàn',
            'role' => 'Vai trò',
            'employee' => 'Nhân viên',
            'customer' => 'Khách hàng',
        ];

        $actions = [
            'view' => 'Xem',
            'create' => 'Thêm',
            'edit' => 'Sửa',
            'delete' => 'Xoá',
        ];

        foreach ($modules as $moduleKey => $moduleName) {
            foreach ($actions as $actionKey => $actionName) {
                Permission::firstOrCreate([
                    'name' => "{$actionKey}_{$moduleKey}",
                ]);
            }
        }

        $manager = Role::firstOrCreate(['name' => 'quanly']);
        $staff = Role::firstOrCreate(['name' => 'nhanvien']);
        $warehouse = Role::firstOrCreate(['name' => 'nhanvienkho']);
        $customer = Role::firstOrCreate(['name' => 'khachhang']);

        $manager->givePermissionTo(Permission::all());

        $staff->givePermissionTo([
            'view_order',
            'create_order',
            'edit_order',
            'view_table',
            'view_food',
            'view_combo',
        ]);

        $warehouse->givePermissionTo([
            'view_food',
            'view_topping',
            'view_combo',
            'view_category',
            'create_food',
            'edit_food',
        ]);

        $customer->givePermissionTo([
            'view_food',
            'view_combo',
            'view_table',
        ]);
    }
}
