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
            'dashboard' => ['view'],
            'order'     => ['view', 'create'],
            'category'  => ['view', 'create', 'edit', 'hidden'],
            'table'     => ['view', 'create', 'edit', 'hidden'],
            'topping'   => ['view', 'create', 'edit', 'hidden'],
            'booking'   => ['view', 'create', 'edit', 'hidden'],
            'role'      => ['view', 'create', 'edit', 'hidden'],
            'employee'  => ['view', 'create', 'edit', 'hidden'],
            'customer'  => ['view', 'create', 'edit', 'hidden'],
            'shipper'   => ['view', 'create', 'edit'],
            'food'      => ['view', 'create', 'edit', 'hidden'],
            'combo'     => ['view', 'create', 'edit', 'hidden'],
            'discounts'     => ['view', 'create', 'edit', 'hidden'],
            'luckyprizes'     => ['view', 'create', 'edit', 'hidden'],
        ];

        foreach ($modules as $moduleKey => $actions) {
            foreach ($actions as $actionKey) {
                Permission::firstOrCreate([
                    'name' => "{$actionKey}_{$moduleKey}",
                ]);
            }
        }

        $manager   = Role::firstOrCreate(['name' => 'quanly']);
        $staff     = Role::firstOrCreate(['name' => 'nhanvien']);
        $warehouse = Role::firstOrCreate(['name' => 'nhanvienkho']);
        $customer  = Role::firstOrCreate(['name' => 'khachhang']);

        $manager->givePermissionTo(Permission::all());

        $staff->givePermissionTo([
            'view_order',
            'create_order',
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
