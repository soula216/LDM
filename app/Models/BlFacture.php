<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlFacture extends Model
{
    use HasFactory;

    protected $table = 'bl_factures';

    protected $fillable = [
        'facture_id',
        'bon_livraison_id',
    ];

    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }

    public function bonLivraison()
    {
        return $this->belongsTo(BonLivraison::class);
    }
}
