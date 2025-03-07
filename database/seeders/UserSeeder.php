<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@koppontren-tarsyub.org',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('admin');

        $pengurus = User::create([
            'name' => 'Pengurus',
            'username' => 'pengurus',
            'email' => 'pengurus@koppontren-tarsyub.org',
            'password' => bcrypt('password'),
        ]);

        $pengurus->assignRole('pengurus');

        $anggota = User::create([
            'name' => 'Anggota',
            'username' => 'anggota',
            'email' => 'anggota@koppontren-tarsyub.org',
            'password' => bcrypt('password'),
        ]);

        $anggota->assignRole('anggota');
    }
}
