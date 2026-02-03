<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandeFile extends Model
{
    use HasFactory;

    protected $table = 'commande_files';

    protected $fillable = [
        'commande_id',
        'type',
        'path',
        'original_name',
        'mime',
        'size',
        'uploaded_by',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
