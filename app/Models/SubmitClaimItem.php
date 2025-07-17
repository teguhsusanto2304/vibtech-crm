<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\IdObfuscator;

class SubmitClaimItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'submit_claim_id',
        'description',
        'amount',
        'currency',
        'claim_type_id',
        'data_status',
        'start_at',
        'end_at'
    ];

    protected $casts = [
        'start_at'=>'datetime',
        'end_at'=>'datetime',
        'created_at'=>'datetime',
    ];

    /**
     * A claim item belongs to a claim.
     */
    public function submitClaim()
    {
        return $this->belongsTo(SubmitClaim::class);
    }

    /**
     * A SubmitClaimItem can have many approvals specific to this item.
     */
    public function submitClaimItemApproval()
    {
        // The foreign key is 'submit_claim_item_id' on the SubmitClaimApproval model.
        // The local key on this model (SubmitClaimItem) is 'id'.
        return $this->hasMany(SubmitClaimApproval::class, 'submit_claim_item_id');
    }

    public function claimType()
    {
        return $this->belongsTo(ClaimType::class);
    }

    public function files() // <--- ADD THIS RELATIONSHIP
    {
        return $this->hasMany(SubmitClaimFile::class);
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
