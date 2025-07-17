<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\IdObfuscator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class SubmitClaim extends Model
{
    const STATUS_DRAFT = 1;
    const STATUS_SUBMIT = 2; // New status for claims awaiting admin action
    const STATUS_APPROVED = 3;
    const STATUS_REJECTED = 4;
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

    public function submitClaimApproval()
    {
        return $this->hasMany(SubmitClaimApproval::class);
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
            self::STATUS_DRAFT => '<span class="badge bg-warning"><small>Draft</small></span>',
            self::STATUS_SUBMIT => '<span class="badge bg-info"><small>Submitted</small></span>',
            self::STATUS_APPROVED => '<span class="badge bg-success"><small>Completed</small></span>',
            self::STATUS_REJECTED => '<span class="badge bg-danger"><small>Rejected</small></span>',
            default => '<span class="badge bg-danger"><small>unknown</small></span>',
        };
    }

    public function getTransferDocumentUrlAttribute()
    {
        if ($this->submitClaimApproval->transfer_document_path) {
            // Ensure the path is correct and accessible via public disk
            return Storage::disk('public')->url($this->transfer_document_path);
        }
        return null;
    }

    public function getSubmitClaimStatusDescriptionAttribute()
    {
        // Get the latest approval/rejection record for this claim
        // Ensure 'approvals' relationship is eager loaded when fetching the SubmitClaim
        // e.g., SubmitClaim::with('approvals')->find($id);
        $latestApproval = $this->submitClaimApproval()->latest()->first(); // This fetches the latest approval

        $statusHtml = '';
        $notesHtml = '';

        switch ($this->data_status) {
            case self::STATUS_APPROVED:
                $statusHtml = '';
                if(!is_null($latestApproval)){
                    $notesHtml = '<div class="mb-3 mt-3"><strong>Transfer Doc:</strong> <div><a href="' . Storage::disk('public')->url($latestApproval->transfer_document_path) . '" target="_blank">View</a></div></div>';
                } else {
                    $notesHtml = '<div class="mb-3 mt-3"><strong>Transfer Doc:</strong> <div>N/A</div></div>';
                }
                
                break;

            case self::STATUS_REJECTED:
                $statusHtml = '';
                
                    
                    
                
                $notesHtml = '<div class="mb-3 mt-3"><strong>Reason:</strong> <div>' . e($latestApproval->notes) . '</div></div>';
                // Check if latestApproval exists, its status is 'rejected', and it has notes
                if ($latestApproval && $latestApproval->status === 'rejected' && $latestApproval->notes) {
                    $notesHtml = '<p class="text-muted mt-1 mb-0"><small>Reason: ' . e($latestApproval->notes) . '</small></p>';
                }
                break;
        }

        return $statusHtml . $notesHtml;
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
            $items = $this->submitClaimItems()->where('data_status',1)->get();
        } else {
            // If not loaded, fetch them (consider eager loading this relationship in your controller)
            $items = $this->submitClaimItems()->where('data_status',1)->get();
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
