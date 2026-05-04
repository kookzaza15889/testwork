<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. User ประชาชน
        User::create([
            'name' => 'Public User',
            'email' => 'public@test.com',
            'password' => Hash::make('123456789'),
            'role' => 'public'
        ]);

        // 2. User เจ้าหน้าที่
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@test.com',
            'password' => Hash::make('123456789'),
            'role' => 'staff'
        ]);
    }
}