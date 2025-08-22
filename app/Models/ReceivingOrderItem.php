<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivingOrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'receiving_order_id',
        'product_id',
        'quantity',
    ];

    /**
     * Dapatkan pesanan penerimaan yang terkait.
     */
    public function receivingOrder()
    {
        return $this->belongsTo(ReceivingOrder::class);
    }

    /**
     * Dapatkan produk yang terkait dengan item ini.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
