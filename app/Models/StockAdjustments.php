<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustments extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'adjust_type',
        'quantity',
        'adjust_number',
        'for_or_from',
        'reason',
        'user_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
