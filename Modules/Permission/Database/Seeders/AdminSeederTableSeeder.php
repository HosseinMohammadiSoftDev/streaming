<?php

namespace Modules\Permission\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeederTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'view user information',
            'upload videos',
            'download videos',
            'role and Permission control'
        ];
            
            foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $adminRole->syncPermissions(Permission::all());

        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole($adminRole);
    }
}
