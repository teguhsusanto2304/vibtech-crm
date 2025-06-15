<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KanbanStage extends Model
{
    use HasFactory;

    public function projectStages() // <--- ADD THIS RELATIONSHIP
    {
        return $this->hasMany(ProjectStage::class);
    }
}
