<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingMinute extends Model
{
    use HasFactory;
    protected $fillable = [
        'topic',
        'meeting_date',
        'start_time',
        'end_time',
        'saved_by_user_id',
        'data_status'
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'start_time' => 'datetime', // Cast as datetime to handle time correctly
        'end_time' => 'datetime',   // Cast as datetime to handle time correctly
    ];

    public function attendees()
    {
        return $this->hasMany(MeetingAttendee::class);
    }

    public function savedBy()
    {
        return $this->belongsTo(User::class, 'saved_by_user_id');
    }
}
