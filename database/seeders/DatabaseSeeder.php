<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // เรียกใช้ UserSeeder ที่เราเพิ่งสร้าง
        $this->call([
            UserSeeder::class,
        ]);
    }
}