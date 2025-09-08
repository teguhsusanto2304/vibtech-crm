<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTask extends Model
{
    use HasFactory;

    /**
     * Konstanta untuk status data_status
     */
    const STATUS_ON_GOING = 1;
    const STATUS_PENDING_REVIEW = 2;
    const STATUS_OVERDUE = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_DELETED = 0;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'project_tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'project_phase_id',
        'project_kanban_stage_id',
        'project_kanban_id',
        'assigned_to_user_id',
        'created_by',
        'name',
        'description',
        'update_log',
        'data_status',
        'progress_percentage',
        'start_at',
        'end_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
        'completed_at' => 'datetime',
    ];

    /* -------------------------- Relasi Model -------------------------- */

    /**
     * Get the project that the task belongs to.
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get the project phase that the task belongs to.
     *
     * @return BelongsTo
     */
    public function projectPhase(): BelongsTo
    {
        return $this->belongsTo(ProjectPhase::class, 'project_phase_id');
    }

    /**
     * Get the project stage that the task belongs to.
     *
     * @return BelongsTo
     */
    public function projectStage(): BelongsTo
    {
        return $this->belongsTo(KanbanStage::class, 'project_stage_id');
    }

    public function projectKanbanStage(): BelongsTo
    {
        return $this->belongsTo(ProjectKanbanStage::class, 'project_kanban_stage_id');
    }
    
    /**
     * Get the project kanban that the task belongs to.
     *
     * @return BelongsTo
     */
    public function projectKanban(): BelongsTo
    {
        return $this->belongsTo(ProjectKanban::class, 'project_kanban_id');
    }

    /**
     * Get the user who the task is assigned to.
     *
     * @return BelongsTo
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Get the user who created the task.
     *
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}