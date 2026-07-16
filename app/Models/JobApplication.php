<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_title',
        'name',
        'email',
        'phone',
        'cover_letter',
        'cv_path',
        'cv_name',
    ];

    public function hasCv(): bool
    {
        return filled($this->cv_path);
    }
}
