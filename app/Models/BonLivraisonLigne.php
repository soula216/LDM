<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonLivraisonLigne extends Model
{
    use HasFactory;

    protected $table = 'bon_livraison_lignes';

    protected $fillable = [
        'bon_livraison_id',
        'service_id',
        'service_name_snapshot',
        'prix_unitaire_ttc_snapshot',
        'quantite',
        'total_ligne_ttc',
    ];

    protected $casts = [
        'prix_unitaire_ttc_snapshot' => 'decimal:2',
        'total_ligne_ttc' => 'decimal:2',
    ];

    public function bonLivraison()
    {
        return $this->belongsTo(BonLivraison::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
