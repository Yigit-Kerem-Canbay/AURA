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
        $adminRole = \App\Models\Role::firstOrCreate(['name' => 'admin']);
        $managerRole = \App\Models\Role::firstOrCreate(['name' => 'kurum_yoneticisi']);
        $employeeRole = \App\Models\Role::firstOrCreate(['name' => 'calisan']);

        \App\Models\User::firstOrCreate(
            ['email' => 'admin@aura.local'],
            [
                'name' => 'Admin User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role_id' => $adminRole->id
            ]
        );
    }
}
