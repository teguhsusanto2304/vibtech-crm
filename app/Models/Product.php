<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\IdObfuscator;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'sku_no',
        'product_category_id', // Tambahkan 'category_id'
        'quantity',
        'image',
        'created_by',
    ];

        protected $casts = [
        'created_at'=>'datetime',
        'updated_at'=>'datetime'
    ];

    public function getObfuscatedIdAttribute(): string
    {
        return IdObfuscator::encode($this->attributes['id']); // <--- Call your encoder
    }

    /**
     * Dapatkan kategori yang dimiliki produk.
     */
    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class,'product_category_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
}
