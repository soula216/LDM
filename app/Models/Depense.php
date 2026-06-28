<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'qte',
        'date',
        'montant',
    ];

    protected $casts = [
        'qte' => 'integer',
        'date' => 'date',
        'montant' => 'decimal:2',
    ];
}
