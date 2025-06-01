<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@a.com'],
            [
                'name' => 'zoki-admin',
                'email' => 'admin@a.com',
                'password' => Hash::make('zoki'),
                'role' => RoleEnum::ADMIN->value,
            ]
        );
    }
}
