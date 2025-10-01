<?php

namespace App\Enums;

enum VehiculeStatus:string
{
    //
     case PENDING = 'en_attente';
    case VALIDATED = 'valide';
    case REJECTED = 'refuse';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::VALIDATED => 'Validé',
            self::REJECTED => 'Refusé',
        };
    }
}
