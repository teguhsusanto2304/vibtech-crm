<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectKanban extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'name',
        'data_status',
        'color_background'
    ];
    public function tasks()
    {
        return $this->hasMany(ProjectTask::class, 'project_kanban_id', 'id');
    }
}
