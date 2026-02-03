<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonLivraison extends Model
{
    use HasFactory;

    protected $table = 'bons_livraison';

    protected $fillable = [
        'commande_id',
        'numero_bl',
        'total_ttc',
        'created_by',
    ];

    protected $casts = [
        'total_ttc' => 'decimal:2',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function lignes()
    {
        return $this->hasMany(BonLivraisonLigne::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function factures()
    {
        return $this->belongsToMany(Facture::class, 'bl_factures', 'bon_livraison_id', 'facture_id');
    }
}
