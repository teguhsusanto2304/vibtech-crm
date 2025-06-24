<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTaskLog extends Model
{
    use HasFactory;

    protected $table = 'project_task_logs'; // Ensure table name is correct

    protected $fillable = [
        'task_id',
        'description',
        'user_id',
    ];

    /**
     * A task log belongs to a ProjectStageTask.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(ProjectStageTask::class, 'task_id');
    }

    /**
     * A task log was created by a User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'name' => 'System/Deleted User', // Default name if user_id is null (due to onDelete('set null'))
        ]);
    }
}
