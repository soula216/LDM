<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandeTache extends Model
{
    use HasFactory;

    protected $table = 'commande_taches';

    protected $fillable = [
        'commande_id',
        'service_id',
        'nb_elem',
        'dents',
        'teinte',
        'date_livraison',
        'prix_unitaire_ttc_snapshot',
        'total_ligne_ttc',
    ];

    protected $casts = [
        'date_livraison' => 'datetime',
        'prix_unitaire_ttc_snapshot' => 'decimal:2',
        'total_ligne_ttc' => 'decimal:2',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Accessor pour obtenir le groupe depuis le service
    public function getGroupeAttribute()
    {
        return $this->service->groupe ?? null;
    }

    public function ficheControleQuality()
    {
        return $this->hasOne(FicheControleQuality::class, 'tache_id');
    }
}
