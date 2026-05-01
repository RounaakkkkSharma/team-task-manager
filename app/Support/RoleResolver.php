<?php

namespace App\Support;

use App\Models\User;

class RoleResolver
{
    public const ADMIN_EMAIL = 'admin@example.com';

    public static function roleForEmail(string $email): string
    {
        // If this is the first user being registered, make them admin
        if (User::count() === 0) {
            return 'admin';
        }

        // Otherwise, check if email matches the admin email for backwards compatibility
        return strtolower($email) === self::ADMIN_EMAIL ? 'admin' : 'member';
    }
}
