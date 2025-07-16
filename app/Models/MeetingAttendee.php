<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_minute_id',
        'user_id',
        'speaker_notes',
        'data_status'
    ];

    public function meetingMinute()
    {
        return $this->belongsTo(MeetingMinute::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
