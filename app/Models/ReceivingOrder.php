<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivingOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'po_number',
        'purchase_date',
        'received_date',
        'created_by',
        'supplier_name',
        'remarks',
        'data_status'
    ];
    protected $casts = [
        'purchase_date' => 'date',
        'received_date' => 'date'
    ];

    /**
     * Dapatkan item-item penerimaan pesanan.
     */
    public function items()
    {
        return $this->hasMany(ReceivingOrderItem::class);
    }

    /**
     * Dapatkan supplier yang terkait dengan pesanan penerimaan.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
