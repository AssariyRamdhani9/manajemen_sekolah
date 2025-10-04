<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin default
        User::create([
            'full_name' => 'Administrator',
            'email' => 'admin@sekolah.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
    }
}
