<?php

namespace App\Enums;

class UserRole
{
    const ADMIN = 0;

    const DC = 1;

    const TAG_OFFICER = 2;

    const UNO = 3;

    public static function list(): array
    {
        return [
            self::ADMIN => 'Admin',
            self::DC => 'DC',
            self::TAG_OFFICER => 'Tag Officer',
            self::UNO => 'UNO',
        ];
    }
}
