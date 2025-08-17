<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ProjectPhase extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_OVERDUE = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_DELETED = 0;
    use HasFactory;
    protected $fillable = [
        'project_id',
        'phase', // Make sure this is fillable if you set it manually
        'name',
        'description',
        'start_date',
        'end_date',
        'data_status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * A project phase belongs to a Project.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function projectStages() // <--- ADD THIS RELATIONSHIP
    {
        return $this->hasMany(ProjectStage::class);
    }

    public function projectStageTasks() // <--- ADD THIS RELATIONSHIP
    {
        return $this->hasMany(ProjectTask::class,'project_phase_id');
    }

    public function getPhaseStatusAttribute(): string
    {
        $result = '';
        if($this->data_status==1){
            $result = 'Phase Ongoing';
        } elseif($this->data_status==2){
            $result ='Phase Pending Review';
        } elseif($this->data_status==3){
            $result = 'Phase Completed';
        } else {
            $result = 'Phase Completed';
        }

        return $result;
    }

    public function getPhaseStatusBadgeAttribute(): string
    {
        $result = '';
        if($this->data_status==1){
            $result = 'bg-warning';
        } elseif($this->data_status==2){
            $result ='bg-info';
        } elseif($this->data_status==3){
            $result = 'bg-success';
        } else {
            $result = 'bg-danger';
        }

        return $result;
    }

    /**
     * Accessor to get the count of completed project stages for this phase.
     *
     * @return int
     */
    public function getCompletedStagesCountAttribute(): int
    {
        // Check if the projectStages relationship has been loaded to avoid N+1 queries
        if ($this->relationLoaded('projectStages')) {
            return $this->projectStages->where('data_status', ProjectStage::STATUS_COMPLETED)->count();
        }

        // If not loaded, query the database directly
        return $this->projectStages()->where('data_status', ProjectStage::STATUS_COMPLETED)->count();
    }

    public function getCompletedStageTasksAttribute(): int
    {
        // Check if the projectStages relationship has been loaded to avoid N+1 queries
        $countAll = $this->projectStageTasks->count();
        if ($this->relationLoaded('projectStageTasks')) {
            $count = $this->projectStageTasks->where('data_status', ProjectTask::STATUS_COMPLETED)->count();
        } else {
            $count = $this->projectStageTasks->where('data_status', ProjectTask::STATUS_COMPLETED)->count(); 
        }
        if($count == $countAll && $countAll > 0){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Accessor to get the CSS badge class based on current date vs end_date.
     *
     * @return string
     */
    public function getStatusDateBadgeAttribute(): string
    {
        // If end_date is null, we might default to primary or handle it differently
        if (is_null($this->end_date)) {
            return 'bg-secondary'; // Or bg-primary, or throw an error, depending on business logic
        }

        // Get the current date (only date part, no time, for accurate comparison)
        $currentDate = Carbon::now()->startOfDay();

        // Get the end_date of the phase (only date part, no time)
        $phaseEndDate = Carbon::parse($this->end_date)->startOfDay();

        // Compare dates
        if ($currentDate->greaterThanOrEqualTo($phaseEndDate)) {
            // Current date is equal to or past the end_date
            return 'bg-danger';
        } else {
            // Current date is before the end_date
            return 'bg-primary';
        }
    }

}
