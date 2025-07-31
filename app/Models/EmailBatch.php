<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailBatch extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'total_recipients',
        'sent_count',
        'failed_count',
        'status',
        'user_id',
    ];
}
