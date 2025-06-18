<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProjectFile extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'project_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'project_stage_task_id',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'description',
        'uploaded_by_user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // No specific casts needed for these columns unless you store dates/times
        'created_at'=>'datetime'
    ];

    /**
     * Get the project that owns the file.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who uploaded the file.
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function task() // <--- ADD THIS RELATIONSHIP
    {
        return $this->belongsTo(ProjectStageTask::class, 'project_stage_task_id');
    }

    /**
     * Get the URL for the file.
     * This is a convenient accessor to generate the public URL for the stored file.
     * Assumes files are stored in `storage/app/public` and symlinked to `public/storage`.
     */
    public function getFileUrlAttribute(): ?string
    {
        // Adjust 'public' to your disk name if different
        return Storage::disk('public')->url($this->file_path);
    }
}
