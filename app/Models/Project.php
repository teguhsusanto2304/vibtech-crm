<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Helpers\IdObfuscator;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'project_manager_id',
        'start_at',
        'end_at',
        'data_status'
    ];
    protected $casts = [
        'start_at'=>'datetime',
        'end_at'=>'datetime'
    ];
    
    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function projectMembers() // <--- This relationship name is fine, but it must be belongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'member_id');
    }

    public function showProjectMembers()
    {
        //return $this->belongsToMany(User::class, 'project_members', 'project_id', 'member_id');
        return $this->hasMany(ProjectMember::class);
    }

    public function getProgressPercentageAttribute(): int
    {
        $startDate = $this->start_at;
        $endDate = $this->end_at;
        $currentDate = Carbon::now();

        // If dates are null, return 0
        if (!$startDate || !$endDate) {
            return 0;
        }

        // Calculate total duration (inclusive of start and end dates)
        // Add 1 day to include both start and end date
        $totalDurationDays = $startDate->diffInDays($endDate) + 1;

        // Calculate days passed from start to current date (inclusive of start)
        // Ensure days passed is not negative if current date is before start date
        $daysPassed = $startDate->diffInDays($currentDate) + 1;

        // Handle edge cases:
        if ($currentDate->lessThan($startDate)) {
            return 0; // Project hasn't started yet
        }

        if ($currentDate->greaterThanOrEqualTo($endDate)) {
            return 100; // Project is finished or overdue
        }

        if ($totalDurationDays <= 0) { // Avoid division by zero for same start/end date
            return 100; // Consider it 100% if it's a 1-day project or misconfigured
        }

        $progress = ($daysPassed / $totalDurationDays) * 100;

        return (int) round($progress); // Return as integer, rounded
    }

    public function getRemainingDaysAttribute(): int
    {
        $endDate = $this->end_at;
        $currentDate = Carbon::now();

        if (!$endDate || $currentDate->greaterThanOrEqualTo($endDate)) {
            return 0; // Project already ended
        }

        return $currentDate->diffInDays($endDate);
    }

    public function getObfuscatedIdAttribute(): string
    {
        return IdObfuscator::encode($this->attributes['id']); // <--- Call your encoder
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return $this->where($field, $value)->first();
        }
        try {
            $decodedId = IdObfuscator::decode($value);
            return $this->where('id', $decodedId)->first();
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    public function projectStages() // <--- ADD THIS RELATIONSHIP
    {
        return $this->hasMany(ProjectStage::class);
    }

    public function getCanCompleteAttribute(): int
    {
        if($this->projectStages->where('data_status', '!=', 2)->count() > 0)
        {
            return false;
        } else {
            if($this->data_status==2){
                return false;
            } else {
                if($this->data_status==1)
                {
                    return false;
                } else {
                    
                return true;
                }
            }
        }
    }

    public function getWorkProgressPercentageAttribute(): int
    {
        return round($this->projectStages->where('data_status', 2)->count() * 12.5);
    }

    public function getCanCreateTaskAttribute(): int
    {
        if($this->showProjectMembers->where('member_id',auth()->user()->id)->count() > 0)
        {
            return true;
        } else {
            return false;
        }
    }

    public function getStatusProjectAttribute(): string
    {
        if($this->data_status==1){
            return 'Active';
        } else {
            return 'Complete';
        }
    }

    public function files() // <--- ADD THIS RELATIONSHIP
    {
        return $this->hasMany(ProjectFile::class);
    }

    public function stageLogs(): HasMany
    {
        return $this->hasMany(ProjectStageLog::class, 'project_id');
    }
}
