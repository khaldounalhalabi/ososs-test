<?php

namespace App;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
