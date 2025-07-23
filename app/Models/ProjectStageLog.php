<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectStageLog extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'project_stage_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'project_phase_id',
        'stage_id',
        'description',
        'created_by', // IMPORTANT: Keep this as 'cerated_by' if that's the exact column name in your DB.
                      // If it's a typo and should be 'created_by', correct it in your migration AND here.
    ];

    /**
     * Get the project that owns the log.
     */
    public function project(): BelongsTo
    {
        // Assumes your Project model is named 'Project' and lives in App\Models
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get the kanban stage that owns the log.
     */
    public function stage(): BelongsTo
    {
        // Assumes your KanbanStage model is named 'KanbanStage' and lives in App\Models
        return $this->belongsTo(KanbanStage::class, 'stage_id');
    }

    /**
     * Get the user who created the log.
     */
    public function createdBy(): BelongsTo
    {
        // Assuming your User model is in App\Models\User
        // IMPORTANT: The foreign key here *must* match your column name, 'cerated_by'
        return $this->belongsTo(User::class, 'created_by');
    }
}
