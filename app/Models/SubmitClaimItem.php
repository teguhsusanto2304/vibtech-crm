<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * A claim item belongs to a claim.
     */
    public function submitClaim()
    {
        return $this->belongsTo(SubmitClaim::class);
    }

    public function files() // <--- ADD THIS RELATIONSHIP
    {
        return $this->hasMany(SubmitClaimFile::class);
    }
}
