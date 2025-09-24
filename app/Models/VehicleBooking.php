<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class VehicleBooking extends Model
{
    use HasFactory;

    protected $fillable = ['vehicle_id', 'start_at', 'end_at', 'purposes', 'job_assignment_id', 'created_by', 'is_booker'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    protected $appends = ['start_at_formatted', 'end_at_formatted'];

    public function vehicle1()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function jobAssignment()
    {
        return $this->belongsTo(JobAssignment::class, 'job_assignment_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStartAtFormattedAttribute($value)
    {
        return Carbon::parse($this->start_at)->format('d-m-Y H:i:s');
    }

    public function getEndAtFormattedAttribute($value)
    {
        return Carbon::parse($this->end_at)->format('d-m-Y H:i:s');
    }
}
