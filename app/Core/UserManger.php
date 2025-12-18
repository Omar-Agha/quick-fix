<?php

namespace App\Core;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManger
{
    public function createOrUpdateUser($id = null, string $email, string $password, string $name, UserRole $role): User
    {
        return User::updateOrCreate(
            ['id' => $id], // search condition
            [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
                'role' => $role,
            ]
        );
    }
}
