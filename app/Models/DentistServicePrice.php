<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentistServicePrice extends Model
{
    use HasFactory;

    protected $table = 'dentist_service_prices';

    protected $fillable = [
        'dentist_id',
        'service_id',
        'prix_unitaire_ttc',
    ];

    protected $casts = [
        'prix_unitaire_ttc' => 'decimal:2',
    ];

    public function dentist()
    {
        return $this->belongsTo(User::class, 'dentist_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
