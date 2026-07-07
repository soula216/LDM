<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'attachment_path',
        'attachment_name',
    ];

    public function hasAttachment(): bool
    {
        return filled($this->attachment_path);
    }
}
