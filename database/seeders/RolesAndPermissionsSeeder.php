<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'manage categories']);
        Permission::create(['name' => 'manage tags']);
        Permission::create(['name' => 'manage tickets']);
        Permission::create(['name' => 'manage priorities']);
        Permission::create(['name' => 'manage roles']);

        $agent = Role::create(['name' => 'agent']);
        $agent->givePermissionTo(['manage categories', 'manage tags', 'manage tickets', 'manage priorities']);

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $requester = Role::create(['name' => 'requester']);
        $requester->givePermissionTo('manage tickets');
    }   
}
