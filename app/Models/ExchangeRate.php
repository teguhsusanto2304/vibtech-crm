<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;
    protected $fillable = ['rate_date',
    'currency',
    'rate'];
    protected $casts = [
        'created_at'=>'datetime',
        'updated_at'=>'datetime',
        'rate_date'=>'date'
    ];
}
