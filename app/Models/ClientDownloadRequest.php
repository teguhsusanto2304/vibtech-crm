<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDownloadRequest extends Model
{
    use HasFactory;
    protected $fillable = ['total_data','file_type',
    'client_id','request_id','approved_id','data_status','expires_at','request_data','unique_token'];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'request_id');
    }

}
