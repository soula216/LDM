<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\CritereQualityType;

class CritereQuality extends Model
{
    use HasFactory;

    protected $table = 'critere_quality';

    protected $fillable = [
        'nom',
        'groupe_id',
        'type',
    ];

    protected $casts = [
        'type' => CritereQualityType::class,
    ];

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }
}
