<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\IdObfuscator;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectStageTask extends Model
{
    const STATUS_ON_GOING = 1;
    const STATUS_PENDING_REVIEW = 2; // New status for claims awaiting admin action
    const STATUS_OVERDUE = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_DELETED = 0;
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'project_stage_tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_stage_id',
        'assigned_to_user_id',
        'name',
        'description',
        'data_status',
        'start_at',
        'end_at',
        'completed_at',
        'progress_percentage',
        'update_log',
        'created_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the project stage that this task belongs to.
     */
    public function projectStage()
    {
        return $this->belongsTo(ProjectStage::class);
    }

    /**
     * Get the user who is assigned to this task.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Get the user who is created this task.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Scope a query to only include completed tasks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('data_status', 1);
    }

    /**
     * Scope a query to only include incompleted tasks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIncompleted($query)
    {
        return $query->where('data_status', '!=', 2);
    }

    public function files() // <--- ADD THIS RELATIONSHIP
    {
        return $this->hasMany(ProjectFile::class);
    }

    public function getTaskObfuscatedIdAttribute(): string
    {
        return IdObfuscator::encode($this->attributes['id']); // <--- Call your encoder
    }

    public function getTaskStatusAttribute(): string
    {
        $result = '';
        if($this->data_status==self::STATUS_ON_GOING){
            $result = 'Task Ongoing';
        } elseif($this->data_status==self::STATUS_PENDING_REVIEW){
            $result ='Task Pending Review';
        } elseif($this->data_status==self::STATUS_OVERDUE){
            $result = 'Task Overdue';
        } else {
            $result = 'Task Completed';
        }

        return $result;
    }

    public function getTaskStatusBadgeAttribute(): string
    {
        $result = '';
        if($this->data_status==1){
            $result = 'bg-warning';
        } elseif($this->data_status==2){
            $result ='bg-info';
        } elseif($this->data_status==3){
            $result = 'bg-danger';
        } else {
            $result = 'bg-success';
        }

        return $result;
    }

    public function getTitleStatus(int $status): string
    {
        $result = '';
        if($status==1){
            $result = 'Task Ongoing';
        } elseif($status==2){
            $result ='Task Pending Review';
        } elseif($status==3){
            $result = 'Task Overdue';
        } else {
            $result = 'Task Completed';
        }
        return $result;
    }

    /**
     * Get the displayable name for a given status value.
     * This is a REGULAR PUBLIC METHOD, can accept parameters.
     *
     * @param string $status The status value
     * @return string The displayable name
     */
    public function getBadgeStatus(int $status): string
    {
        $result = '';
        if($status==1){
            $result = 'bg-warning';
        } elseif($status==2){
            $result ='bg-info';
        } elseif($status==3){
            $result = 'bg-danger';
        } else {
            $result = 'bg-success';
        }

        return $result;
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ProjectTaskLog::class, 'task_id');
    }

    public function kanbanStatus()
    {
        return $this->belongsTo(Kanban::class, 'data_status');
    }

    
}
