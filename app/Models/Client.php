<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'company',
        'position',
        'email',
        'office_number',
        'mobile_number',
        'job_title',
        'industry_category_id',
        'country_id',
        'sales_person_id',
        'contact_for_id',
        'created_id',
        'updated_id',
        'deleted_id',
        'deleted_at',
        'image_path',
        'remark',
        'is_editable',
        'is_deletable',
        'data_status',
        'client_type'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getClientTypeLabelAttribute()
    {
        return match ($this->client_type) {
            1 => '<span class="badge bg-primary"><small>Vendor</small></span>',
            2 => '<span class="badge bg-warning"><small>Supplier</small></span>',
            3 => '<span class="badge bg-success"><small>Client</small></span>',
            default => '<span class="badge bg-danger"><small>Unknown</small></span>',
        };
    }

    public function activityLogs()
    {
        return $this->hasMany(ClientActivityLog::class)->orderBy('created_at', 'desc');;
    }

    public function industryCategory(): BelongsTo
    {
        return $this->belongsTo(IndustryCategory::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function salesPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_person_id');
    }

    public function contactFor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contact_for_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_id');
    }

        public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_id');
    }

    public function clientRequests(): HasMany
    {
        return $this->hasMany(ClientRequest::class, 'client_id');
    }

    public function remarks(): HasMany
    {
        return $this->hasMany(ClientRemark::class,'client_id');
    }

    protected static function booted()
    {
        //static::created(function ($client) {

        //});
    }
}
