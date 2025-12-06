<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //  gọi seeder PermissionSeeder
        $this->call(PermissionSeeder::class);
        //  gọi seeder RoleSeeder
        $this->call(RoleSeeder::class);
        //  gọi seeder AdminSeeder
        $this->call(AdminSeeder::class);
    }
}
