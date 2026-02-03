<?php

namespace App\Enums;

enum CommandeStatus: string
{
    case RECUE = 'Reçue';
    case EN_COURS = 'En cours';
    case TERMINEE = 'Terminée';
    case LIVREE = 'Livrée';

    public function label(): string
    {
        return match($this) {
            self::RECUE => 'Reçue',
            self::EN_COURS => 'En cours',
            self::TERMINEE => 'Terminée',
            self::LIVREE => 'Livrée',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
