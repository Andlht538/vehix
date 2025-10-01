<?php

namespace App\Enums;

enum UserRole
{
    //
    case CLIENT;
    case VALIDATOR;
    case ADMIN;

    public function label(): string
    {
        return match($this) {
            self::CLIENT => 'Client',
            self::VALIDATOR => 'Validateur',
            self::ADMIN => 'Administrateur',
        };
    }

}
