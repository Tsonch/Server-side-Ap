<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UsersAndRoles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Admin = [
            'username' => 'Adminnn', 'email' => 'admin@admin.com', 'password' => 'Admin@111', 'birthday' => '2004.12.12'
        ];

        User::create($Admin);
    }   
}
