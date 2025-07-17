<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SubmitClaimApproval extends Model
{
    use HasFactory;
    const STATUS_APPROVED = 3;
    const STATUS_REJECTED = 4;
    protected $fillable = [
        'submit_claim_id',
        'submit_claim_item_id',
        'approved_by_user_id',
        'data_status',
        'notes',
        'transfered_at',
        'transfer_document_path',
    ];

    protected $casts = [
        'transfered_at'=>'datetime',
        'created_at'=>'datetime',
    ];

    // Relationships
    public function submitClaim()
    {
        return $this->belongsTo(SubmitClaim::class);
    }

    /**
     * An approval can optionally belong to a specific SubmitClaimItem.
     */
    public function submitClaimItem()
    {
        // The foreign key is 'submit_claim_item_id' on this model.
        // The local key on the related model (SubmitClaimItem) is 'id'.
        return $this->belongsTo(SubmitClaimItem::class, 'submit_claim_item_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function getSubmitClaimStatusDescriptionAttribute()
    {
        return match ($this->data_status) {
            self::STATUS_APPROVED => '<p>'.Storage::disk('public')->url($this->transfer_document_path).'</p>',
            self::STATUS_REJECTED => '<span class="badge bg-danger"><small>Rejected</small></span>',
            default => '<span class="badge bg-danger"><small>unknown</small></span>',
        };
    }

    // Accessor to get the public URL of the transfer document
    public function getTransferDocumentUrlAttribute()
    {
        if ($this->transfer_document_path) {
            return Storage::disk('public')->url($this->transfer_document_path);
        }
        return null;
    }
}
