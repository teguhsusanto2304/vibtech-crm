<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'image_path',
        'data_status'
    ];

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
}
