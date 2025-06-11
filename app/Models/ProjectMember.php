<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'member_id',
        'data_status'
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
