<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function taches()
    {
        return $this->hasMany(CommandeTache::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function criteresQuality()
    {
        return $this->hasMany(CritereQuality::class);
    }
}
