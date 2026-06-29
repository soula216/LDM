<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $fillable = [
        'element_id',
        'qte',
    ];

    protected $casts = [
        'qte' => 'integer',
    ];

    public function element(): BelongsTo
    {
        return $this->belongsTo(Element::class);
    }
}
