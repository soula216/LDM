<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'dentiste_id',
        'num_cmd',
        'nom_patient',
        'urgent',
        'status',
        'commentaire',
        'created_by',
        'finished_by',
    ];

    protected $casts = [
        'urgent' => 'boolean',
    ];

    public function dentiste()
    {
        return $this->belongsTo(User::class, 'dentiste_id');
    }

    public function taches()
    {
        return $this->hasMany(CommandeTache::class);
    }

    public function files()
    {
        return $this->hasMany(CommandeFile::class);
    }

    public function bonLivraison()
    {
        return $this->hasOne(BonLivraison::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function finishedBy()
    {
        return $this->belongsTo(User::class, 'finished_by');
    }
}
