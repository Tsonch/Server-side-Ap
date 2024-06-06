<?php

namespace Database\Seeders;

use App\Models\Permissions;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreatePermissions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'get-list-user', 'description' => 'can get list of users', 'encryption' => '1', 'created_by' => '1'],
            ['name' => 'read-user', 'description' => 'can read user', 'encryption' => '2', 'created_by' => '1'],
            ['name' => 'create-user', 'description' => 'can create user', 'encryption' => '3', 'created_by' => '1'],
            ['name' => 'update-user', 'description' => 'can update user', 'encryption' => '4', 'created_by' => '1'],
            ['name' => 'delete-user', 'description' => 'can delete user', 'encryption' => '5', 'created_by' => '1'],
            ['name' => 'restore-user', 'description' => 'can restore user', 'encryption' => '6', 'created_by' => '1'],


            ['name' => 'get-list-role', 'description' => 'can get list of roles', 'encryption' => '7', 'created_by' => '1'],
            ['name' => 'read-role', 'description' => 'can read role', 'encryption' => '8', 'created_by' => '1'],
            ['name' => 'create-role', 'description' => 'can create role', 'encryption' => '9', 'created_by' => '1'],
            ['name' => 'update-role', 'description' => 'can update role', 'encryption' => '10', 'created_by' => '1'],
            ['name' => 'delete-role', 'description' => 'can delete role', 'encryption' => '11', 'created_by' => '1'],
            ['name' => 'restore-role', 'description' => 'can restore deleted role', 'encryption' => '12', 'created_by' => '1'],

            ['name' => 'get-list-permission', 'description' => 'can get list of permissions', 'encryption' => '13', 'created_by' => '1'],
            ['name' => 'read-permission', 'description' => 'can read permission', 'encryption' => '14', 'created_by' => '1'],
            ['name' => 'create-permission', 'description' => 'can create permission', 'encryption' => '15', 'created_by' => '1'],
            ['name' => 'update-permission', 'description' => 'can update permission', 'encryption' => '16', 'created_by' => '1'],
            ['name' => 'delete-permission', 'description' => 'can delete permission', 'encryption' => '17', 'created_by' => '1'],
            ['name' => 'restore-permission', 'description' => 'can restore permission', 'encryption' => '18', 'created_by' => '1'],
        ];

        foreach($permissions as $permission) {
            Permissions::create($permission);
        }
    }
}
