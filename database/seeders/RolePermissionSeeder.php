<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'owner']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'cashier']);

        // Create permissions
        Permission::create(['name' => 'create-post', 'guard_name' => 'api']);
        Permission::create(['name' => 'edit-post', 'guard_name' => 'api']);
        Permission::create(['name' => 'view-post', 'guard_name' => 'api']);
        Permission::create(['name' => 'delete-post', 'guard_name' => 'api']);

        //assign permissions to admin
        $adminRole->givePermissionTo(['create-post', 'edit-post', 'view-post', 'delete-post']);
    }
}
