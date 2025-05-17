<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@testmail.me'],
            [
                'name' => 'useradmin',
                'password' => Hash::make('Ua741236'),
                'role' => UserRole::ADMIN,
            ]
        );
    }
}
