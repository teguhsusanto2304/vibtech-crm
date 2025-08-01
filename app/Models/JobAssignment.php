<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_record_id',
        'job_type_id',
        'job_type',
        'business_name',
        'business_address',
        'scope_of_work',
        'start_at',
        'end_at',
        'vehicle_id',
        'is_vehicle_require',
        'user_id',
        'job_status',
        'is_publish',
    ];

    protected $casts = [
        'created_at'=>'datetime',
        'updated_at'=>'datetime',
        'start_at'=>'date',
        'end_at'=>'date'
    ];

    public function jobType()
    {
        return $this->belongsTo(JobType::class, 'job_type_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function personnel()
    {
        return $this->belongsToMany(User::class, 'job_assignment_personnels', 'job_assignment_id', 'user_id');
    }

    public function vehicleBookings()
    {
        return $this->hasMany(VehicleBooking::class, 'job_assignment_id');
    }

    public function files() // <--- ADD THIS RELATIONSHIP
    {
        return $this->hasMany(JobAssignmentFile::class);
    }
}
