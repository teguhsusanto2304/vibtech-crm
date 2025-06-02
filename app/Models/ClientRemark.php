<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientRemark extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'content',
        'user_id', // if you want to track who made the remark
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user who created the remark (optional).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
