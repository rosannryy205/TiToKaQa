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
            'post'     => ['view', 'create', 'edit', 'hidden'],
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
        
        $customer  = Role::firstOrCreate(['name' => 'khachhang']);

        $manager->givePermissionTo(Permission::all());

        $staff->givePermissionTo([
            'view_order',
            'create_order',
            'view_table',
            'view_food',
            'view_combo',
        ]);

        $customer->givePermissionTo([
            'view_food',
            'view_combo',
            'view_table',
        ]);
    }
}
