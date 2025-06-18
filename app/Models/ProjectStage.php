<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\IdObfuscator;

class ProjectStage extends Model
{
    use HasFactory;
    protected $table = 'project_stages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'kanban_stage_id',
        'data_status',
        'completed_at',
        'completed_task',
        'incompleted_task',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Get the project that owns the project stage.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the predefined kanban stage that this project stage refers to.
     */
    public function kanbanStage()
    {
        return $this->belongsTo(KanbanStage::class);
    }

    /**
     * Get the tasks for the project stage.
     */
    public function tasks() // <--- ADD THIS RELATIONSHIP
    {
        return $this->hasMany(ProjectStageTask::class);
    }

    public function getCompletedTaskAttribute(): int
    {
        // Assuming your ProjectStageTask model has a 'status' column
        return $this->tasks()->where('data_status', 3)->count();
    }

    public function getIncompletedTaskAttribute(): int
    {
        return $this->tasks()->where('data_status', '!=', 3)->count();
    }

    public function getObfuscatedIdAttribute(): string
    {
        return IdObfuscator::encode($this->attributes['id']); // <--- Call your encoder
    }

    public function getStatusStageAttribute(): string
    {
        if ($this->data_status == 1) {
            return 'Active';
        } else {
            return 'Complete';
        }
    }
}
