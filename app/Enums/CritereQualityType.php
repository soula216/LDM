<?php

namespace App\Enums;

enum CritereQualityType: string
{
    case EMPREINTE = 'Empreinte';
    case CONTROLE_VISUEL = 'Contrôle visuel';
    case OCCLUSION = 'Occlusion';
    case LIVRAISON = 'Livraison';
    case MARQUE_MATERIAUX = 'Marque des Matériaux';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            'Empreinte' => 'Empreinte',
            'Contrôle visuel' => 'Contrôle visuel',
            'Occlusion' => 'Occlusion',
            'Livraison' => 'Livraison',
            'Marque des Matériaux' => 'Marque des Matériaux',
        ];
    }
}