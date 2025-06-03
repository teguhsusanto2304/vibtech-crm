<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientDownloadRequest extends Model
{
    use HasFactory;
    protected $fillable = ['total_data',
    'client_id','request_id','approved_id','data_status'];
}
