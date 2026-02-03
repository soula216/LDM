<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'prix_unitaire_ttc',
        'groupe_id',
    ];

    protected $casts = [
        'prix_unitaire_ttc' => 'decimal:2',
    ];

    public function taches()
    {
        return $this->hasMany(CommandeTache::class);
    }

    public function dentistPrices()
    {
        return $this->hasMany(DentistServicePrice::class);
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }
}
