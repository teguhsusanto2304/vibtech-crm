<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\IdObfuscator;

class SubmitClaim extends Model
{
    use HasFactory;
    protected $fillable = [
        'serial_number',
        'claim_date',
        'staff_id',
        'total_amount',
        'currency',
        'data_status',
    ];

    /**
     * A claim has many claim items.
     */
    public function submitClaimItems()
    {
        return $this->hasMany(SubmitClaimItem::class);
    }

    /**
     * A claim belongs to a staff member (User).
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function getObfuscatedIdAttribute(): string
    {
        return IdObfuscator::encode($this->attributes['id']); // <--- Call your encoder
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return $this->where($field, $value)->first();
        }
        try {
            $decodedId = IdObfuscator::decode($value);
            return $this->where('id', $decodedId)->first();
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

}
