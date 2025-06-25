<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department',
        'position',
        'branch_office',
        'path_image',
        'user_number',
        'joined_at',
        'dob',
        'phone_number',
        'position_level_id',
        'department_id',
        'nick_name',
        '2nd_department_id',
        'user_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date',
        'joined_at' => 'date',
        'password' => 'hashed',
    ];

    public function dept()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function secondDept()
    {
        return $this->belongsTo(Department::class, '2nd_department_id', 'id');
    }

    public function assignedJobs()
    {
        return $this->belongsToMany(JobAssignment::class, 'job_assignment_personnels', 'user_id', 'job_assignment_id');
    }

    public function jobAssignmentPersonnel()
    {
        return $this->hasMany(JobAssignmentPersonnel::class, 'user_id', 'id'); // Adjust foreign key names if needed
    }

    public function chatGroups()
    {
        return $this->belongsToMany(ChatGroup::class, 'chat_group_members', 'user_id', 'chat_group_id')
            ->withTimestamps();
    }

    public function getInitialsAttribute(): string
    {
        $initials = '';
        $nameParts = explode(' ', $this->name);
        foreach ($nameParts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }
        return empty($initials) ? '?' : $initials;
    }
    
    public function getAvatarUrlAttribute(): string
    {
        if ($this->path_image) {
            return asset($this->path_image);
        }

        // Generate placeholder URL with initials
        $initials = $this->initials; // Access the initials accessor
        $placeholderUrl = 'https://placehold.co/45x45/d0c5f3/333333?text=' . urlencode($initials);
        return $placeholderUrl;
    }
}
