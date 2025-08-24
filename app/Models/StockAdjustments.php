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
        'user_id',
        'adjustment_type',
        'quantity_adjusted',
        'previous_quantity',
        'new_quantity',
        'po_number',
        'source',
        'purchase_date',
        'received_date',
        'draw_out_date',
        'notes',
    ];

    protected $casts = [
        'purchase_date'=>'datetime',
        'received_date'=>'datetime',
        'draw_out_date'=>'datetime',
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
