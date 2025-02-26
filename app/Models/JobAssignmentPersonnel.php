<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAssignmentPersonnel extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'job_assignment_id',
        'assignment_status',
        'reason',
        'purpose_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
