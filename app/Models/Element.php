<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Element extends Model
{
    protected $fillable = [
        'nom',
    ];

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }
}
