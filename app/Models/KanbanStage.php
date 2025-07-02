<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KanbanStage extends Model
{
    use HasFactory;

    public function projectStages() // <--- ADD THIS RELATIONSHIP
    {
        return $this->hasMany(ProjectStage::class);
    }

    public function projectStageLogs(): HasMany
    {
        return $this->hasMany(ProjectStageLog::class, 'stage_id');
    }
}
