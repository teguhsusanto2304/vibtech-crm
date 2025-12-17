<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'leave_applications';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'country_code',
        'leave_date',
        'title',
        'description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'leave_date' => 'date',
    ];
}
