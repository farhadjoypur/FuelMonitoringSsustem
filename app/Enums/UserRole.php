<?php

namespace App\Enums;

class UserRole
{
    const ADMIN = 0;

    const DC = 1;

    const TAG_OFFICER = 2;

    public static function list(): array
    {
        return [
            self::ADMIN => 'admin',
            self::DC => 'user',
            self::TAG_OFFICER => 'tag officer',
        ];
    }
}
