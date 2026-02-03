<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Echeance extends Model
{
    use HasFactory;

    protected $fillable = [
        'facture_id',
        'montant',
        'mode_reglement',
        'date',
        'statut_paiement',
    ];

    protected $casts = [
        'date' => 'date',
        'montant' => 'decimal:2',
    ];

    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }

    /**
     * Mapping entre les valeurs enum et les valeurs affichées
     */
    private static function getModeReglementMapping()
    {
        return [
            'especes' => 'Espèces',
            'virement_bancaire' => 'Virement bancaire',
            'lettre_change' => 'Lettre de change (كمبيالة)',
        ];
    }

    /**
     * Mapping inverse (affichage vers enum)
     */
    private static function getModeReglementReverseMapping()
    {
        return [
            'Espèces' => 'especes',
            'Virement bancaire' => 'virement_bancaire',
            'Lettre de change (كمبيالة)' => 'lettre_change',
        ];
    }

    /**
     * Get the formatted mode_reglement attribute (enum vers affichage)
     */
    public function getModeReglementFormattedAttribute()
    {
        $mapping = self::getModeReglementMapping();
        return $mapping[$this->mode_reglement] ?? $this->mode_reglement;
    }

    /**
     * Set the mode_reglement attribute (affichage vers enum)
     */
    public function setModeReglementAttribute($value)
    {
        $reverseMapping = self::getModeReglementReverseMapping();
        $this->attributes['mode_reglement'] = $reverseMapping[$value] ?? $value;
    }
}
