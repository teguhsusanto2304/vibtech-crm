<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleBooking extends Model
{
    use HasFactory;

    protected $fillable = ['vehicle_id', 'start_at', 'end_at', 'purposes', 'job_assignment_id', 'created_by'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
