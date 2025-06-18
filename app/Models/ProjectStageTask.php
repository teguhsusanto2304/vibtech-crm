<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStageTask extends Model
{
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
        'progress_percentage'
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
}
