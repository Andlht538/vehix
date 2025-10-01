<?php

namespace App\Enums;

enum VehiculeStatus: string
{
    case PENDING = 'en_attente';
    case VALIDATED = 'valide';
    case REJECTED = 'refuse';
    case TO_CORRECT = 'a_corriger';
    case DUPLICATE = 'doublon';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::VALIDATED => 'Validé',
            self::REJECTED => 'Refusé',
            self::TO_CORRECT => 'À corriger',
            self::DUPLICATE => 'Doublon détecté',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::PENDING => 'Véhicule en cours de validation',
            self::VALIDATED => 'Véhicule validé et actif',
            self::REJECTED => 'Véhicule refusé par le validateur',
            self::TO_CORRECT => 'Corrections requises avant validation',
            self::DUPLICATE => 'Véhicule déjà existant dans le système',
        };
    }

    public function canEdit(): bool
    {
        return match($this) {
            self::PENDING, self::TO_CORRECT, self::REJECTED, self::DUPLICATE => true,
            self::VALIDATED => false,
        };
    }

    public function canAccess(): bool
    {
        return $this === self::VALIDATED;
    }
}