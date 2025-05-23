<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventUserRead extends Model
{
    use HasFactory;

    protected $casts = [
        'read_at' => 'datetime', // <-- Tambahkan baris ini
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
