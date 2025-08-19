<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectKanbanStage extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'name',
        'data_status',
    ];
    public function tasks()
    {
        return $this->hasMany(ProjectTask::class, 'project_kanban_stage_id', 'id');
    }
}
