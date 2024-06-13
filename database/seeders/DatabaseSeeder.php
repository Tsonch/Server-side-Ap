<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CreateAdmin::class);
        $this->call(CreateRoles::class);
        $this->call(CreatePermissions::class);
        $this->call(CreateRoleAndPermission::class);
    }
}
