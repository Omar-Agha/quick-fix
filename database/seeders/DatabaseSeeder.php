<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        User::updateOrCreate(
            ['email' => 'admin@admin.com'], // search condition
            [
                'name' => 'admin',
                'password' => Hash::make('123456789'),
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
                'role' => UserRole::ADMIN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'debugger@admin.com'], // search condition
            [
                'name' => 'debug admin',
                'password' => Hash::make('1q2w#e0'),
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
                'role' => UserRole::DEBUG_ADMIN,
            ]
        );
    }
}
