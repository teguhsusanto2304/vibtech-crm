<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
    //protected $appends = ['short_file_name'];

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

    /**protected function shortFileName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->getShortFileNameAttributeLogic($attributes['file_name']),
        );
    }

    private function getShortFileNameAttributeLogic(?string $fullFileName): ?string
    {
        if (empty($fullFileName)) {
            return null; // Handle cases where file_name might be null
        }

        $maxLength = 25; // Desired maximum length for the *entire* filename

        $pathInfo = pathinfo($fullFileName);
        $baseName = $pathInfo['filename'] ?? ''; // Safely get base name
        $extension = $pathInfo['extension'] ?? ''; // Safely get extension

        // If there's no extension, total length calculation is simpler
        $hasExtension = !empty($extension);
        $dotLength = $hasExtension ? 1 : 0; // Length for the '.' if extension exists

        $currentBaseLength = strlen($baseName);
        $currentTotalLength = $currentBaseLength + $dotLength + strlen($extension);

        if ($currentTotalLength <= $maxLength) {
            return $fullFileName; // No shortening needed
        }

        // Calculate available length for the base name after accounting for extension and dot
        $allowedBaseLength = $maxLength - (strlen($extension) + $dotLength);

        $truncatedBaseName = $baseName;

        // Ensure there's enough space for the ellipsis and at least 1 character
        if ($allowedBaseLength > 3) {
            // Keep the beginning, add ellipsis
            $truncatedBaseName = substr($baseName, 0, $allowedBaseLength - 3) . '...';
        } else if ($allowedBaseLength > 0) {
            // Not enough space for "...", just truncate aggressively
            $truncatedBaseName = substr($baseName, 0, $allowedBaseLength);
        } else {
            // If allowedBaseLength is 0 or negative, it means the extension alone is too long.
            // In this very rare case, we might just return the extension or a truncated extension.
            // For now, we'll make sure it's at least '...' plus extension if possible.
            $truncatedBaseName = ''; // Effectively just the extension will remain or truncated
        }

        // Reconstruct the short filename
        $shortFilename = $truncatedBaseName;
        if ($hasExtension) {
            $shortFilename .= '.' . $extension;
        }

        return $shortFilename;
    }

    public function getShortFileNamexAttribute(): ?string
    {
        $maxLength = 25; // Desired maximum length for the *entire* filename

        //$pathInfo = pathinfo($this->file_name);
        $baseName = $this->file_name; // 11_mercurymagazines43_w_wile725_zqAMNhIkxGKms0OzXYI6DQxxx
        $extension = 'pdf'; // pdf

        $truncatedBaseName = $baseName;

        if (strlen($baseName) + strlen($extension) + 1 > $maxLength) { // +1 for the dot
            $allowedBaseLength = $maxLength - (strlen($extension) + 1);
            if ($allowedBaseLength > 3) { // Ensure there's space for "..."
                $truncatedBaseName = substr($baseName, 0, $allowedBaseLength - 3) . '...';
            } else {
                // If not enough space for "...", just truncate aggressively
                $truncatedBaseName = substr($baseName, 0, $allowedBaseLength);
            }
        }

        $shortFilename = $truncatedBaseName . '.' . $extension;
        return $shortFilename;
    }**/
}
