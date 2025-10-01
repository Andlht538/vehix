<?php

namespace App\Enums;

enum VehiculeStatus
$table->dropColumn(['role', 'login_attempts', 'blocked_until']);
{
    //
    case PENDING;
    case VALIDATED;
    case REJECTED;

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::VALIDATED => 'Validé',
            self::REJECTED => 'Refusé',
        };
    }
}
