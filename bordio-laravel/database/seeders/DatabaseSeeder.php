<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'manage tasks']);
        Permission::create(['name' => 'manage projects']);
        Permission::create(['name' => 'manage users']);

        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'user']);
        $role->givePermissionTo('manage tasks');

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(['manage tasks', 'manage projects']);

        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());

        // Create Super Admin User
        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'subedigokul119@gmail.com',
            'password' => Hash::make('password'),
            'avatar' => 'https://ui-avatars.com/api/?name=Super+Admin&background=0D8ABC&color=fff',
            'role' => 'Super Admin',
        ]);
        $user->assignRole('super-admin');

        // Create Demo Admin User
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@bordio.com',
            'password' => Hash::make('password'),
            'avatar' => 'https://ui-avatars.com/api/?name=Admin+User&background=6c757d&color=fff',
            'role' => 'Admin',
        ]);
        $admin->assignRole('admin');

        // Create Demo Standard User
        $webUser = User::factory()->create([
            'name' => 'Standard User',
            'email' => 'user@bordio.com',
            'password' => Hash::make('password'),
            'avatar' => 'https://ui-avatars.com/api/?name=Standard+User&background=28a745&color=fff',
            'role' => 'User',
        ]);
        $webUser->assignRole('user');
    }
}
