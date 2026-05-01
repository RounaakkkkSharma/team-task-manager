<?php

namespace App\Support;

class RoleResolver
{
    public const ADMIN_EMAIL = 'admin@example.com';

    public static function roleForEmail(string $email): string
    {
        return strtolower($email) === self::ADMIN_EMAIL ? 'admin' : 'member';
    }
}
