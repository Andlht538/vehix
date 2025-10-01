<?php

namespace App\Enums;

enum UserRole
{
    //
    case CLIENT = 'client';
    case VALIDATOR = 'validator';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match($this) {
            self::CLIENT => 'Client',
            self::VALIDATOR => 'Validateur',
            self::ADMIN => 'Administrateur',
        };
    }

}
