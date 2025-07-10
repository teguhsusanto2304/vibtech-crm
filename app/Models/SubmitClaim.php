<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\IdObfuscator;
use Illuminate\Support\Collection;

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
        'description'
    ];

    protected $casts = [
        'claim_date'=>'datetime'
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

    public function getSubmitClaimStatusAttribute()
    {
        return match ($this->data_status) {
            1 => '<span class="badge bg-warning"><small>Draft</small></span>',
            2 => '<span class="badge bg-info"><small>Submitted</small></span>',
            3 => '<span class="badge bg-success"><small>Approved</small></span>',
            4 => '<span class="badge bg-danger"><small>Rejected</small></span>',
            default => '<span class="badge bg-danger"><small>unknown</small></span>',
        };
    }

    /**
     * Get the sum of submit claim items grouped by currency.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTotalByCurrencyAttribute(): Collection
    {
        // Check if the relationship is loaded to prevent N+1 query issue
        if ($this->relationLoaded('submitClaimItems')) {
            $items = $this->submitClaimItems;
        } else {
            // If not loaded, fetch them (consider eager loading this relationship in your controller)
            $items = $this->submitClaimItems()->get();
        }

        return $items->groupBy('currency') // Group by the 'currency' attribute of items
                     ->map(function ($itemsInGroup, $currency) {
                         // Sum the 'amount' for each group
                         $totalAmount = $itemsInGroup->sum('amount');
                         return [
                             'currency' => $currency,
                             'total' => $totalAmount,
                             'formatted_total' => $currency . ' ' . number_format($totalAmount, 2)
                         ];
                     })
                     ->values(); // Reset keys to ensure it's an array of objects/arrays
    }

}
