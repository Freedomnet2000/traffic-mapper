<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@testmail.me'],
            [
                'name' => 'useradmin',
                'password' => Hash::make('Ua741236'),
                'role' => 'admin',
            ]
        );
    }
}
