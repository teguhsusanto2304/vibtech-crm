<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmitClaimFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'submit_claim_item_id',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'description',
        'uploaded_by_user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // No specific casts needed for these columns unless you store dates/times
        'created_at'=>'datetime'
    ];
    //protected $appends = ['short_file_name'];

    /**
     * Get the project that owns the file.
     */
    public function submitClaimItem()
    {
        return $this->belongsTo(submitClaimItem::class);
    }

    /**
     * Get the user who uploaded the file.
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
