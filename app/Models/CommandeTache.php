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
        'custom_service',
        'groupe_id',
        'nb_elem',
        'dents',
        'teinte',
        'date_livraison',
        'calendar_sort_order',
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

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    public function getServiceNomAttribute(): string
    {
        if (filled($this->custom_service)) {
            return $this->custom_service;
        }

        return $this->service?->nom ?? '-';
    }

    public function isCustomService(): bool
    {
        return filled($this->custom_service);
    }

    public function ficheControleQuality()
    {
        return $this->hasOne(FicheControleQuality::class, 'tache_id');
    }
}
