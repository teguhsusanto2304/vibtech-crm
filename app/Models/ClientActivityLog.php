<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientActivityLog extends Model
{
    use HasFactory;
    protected $fillable = ['client_id','activity','action','created_at','updated_at'];
}
