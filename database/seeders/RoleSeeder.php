<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Role
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'middle_admin']);
        Role::create(['name' => 'user']);
    }
}