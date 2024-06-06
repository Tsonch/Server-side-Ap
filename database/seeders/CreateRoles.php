<?php

namespace Database\Seeders;

use App\Models\Roles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateRoles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'description' => 'Admin', 'encryption' => '1', 'created_by' => '1'],
            ['name' => 'User', 'description' => 'User', 'encryption' => '2', 'created_by' => '1'],
            ['name' => 'Guest', 'description' => 'Guest', 'encryption' => '3', 'created_by' => '1']
        ];

        foreach($roles as $role) {
            Roles::create($role);
        }
    }
}
