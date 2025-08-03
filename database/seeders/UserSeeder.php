<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

         User::create([
            'name' => 'Ayu Admin',
            'email' => 'ayu.admin@mail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Raka Admin',
            'email' => 'raka.admin@mail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Users Biasa
        User::create([
            'name' => 'Karyawan',
            'email' => 'user@mail.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@mail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Sari Wijaya',
            'email' => 'sari@mail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Doni Saputra',
            'email' => 'doni@mail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Lina Maulida',
            'email' => 'lina@mail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Ahmad Rizal',
            'email' => 'rizal@mail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Wulan Setiawan',
            'email' => 'wulan@mail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // Superadmin (opsional, aktifin kalau perlu)
        // User::create([
        //     'name' => 'Superadmin',
        //     'email' => 'superadmin@mail.com',
        //     'password' => Hash::make('super123'),
        //     'role' => 'superadmin',
        // ]);
    }
}
