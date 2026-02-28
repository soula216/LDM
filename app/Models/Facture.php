<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facture extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'num_facture',
        'date',
        'dentist_id',
        'titre_document',
        'montant',
        'ancien_solde',
        'avance',
        'status',
        'montant_paye',
        'montant_restant',
    ];

    protected $casts = [
        'date' => 'date',
        'montant' => 'decimal:2',
        'ancien_solde' => 'decimal:2',
        'avance' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'montant_restant' => 'decimal:2',
    ];

    public function dentist()
    {
        return $this->belongsTo(User::class, 'dentist_id');
    }

    public function blFactures()
    {
        return $this->hasMany(BlFacture::class);
    }

    public function bonsLivraison()
    {
        return $this->belongsToMany(BonLivraison::class, 'bl_factures', 'facture_id', 'bon_livraison_id');
    }

    public function echeances()
    {
        return $this->hasMany(Echeance::class);
    }

    /**
     * Get the document title label (Facture or Bon De Livraison)
     */
    public function getTitreDocumentLabelAttribute()
    {
        return self::getTitreDocumentOptions()[$this->titre_document ?? 'bon_livraison'] ?? 'Bon De Livraison';
    }

    /**
     * Get all available document title options
     */
    public static function getTitreDocumentOptions()
    {
        return [
            'facture' => 'Facture',
            'bon_livraison' => 'Bon De Livraison',
        ];
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'En attente',
            'delivered' => 'Envoyé',
            'paid' => 'Payée',
            'partially_paid' => 'Payé partiellement',
            'rejected' => 'Rejetée',
            default => $this->status,
        };
    }

    /**
     * Get all available statuses
     */
    public static function getStatuses()
    {
        return [
            'pending' => 'En attente',
            'delivered' => 'Envoyé',
            'paid' => 'Payée',
            'partially_paid' => 'Payé partiellement',
            'rejected' => 'Rejetée',
        ];
    }
}
