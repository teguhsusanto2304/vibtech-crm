<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleStatus extends Model
{
    use HasFactory;
    protected $fillable = ['role_id', 'data_status', 'details'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
