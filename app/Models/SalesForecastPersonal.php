<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesForecastPersonal extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_forecast_personals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_forecasts_id',
        'personal_id',
        'data_status',
    ];

    // --- Relationships ---

    /**
     * Get the sales forecast that this personal record belongs to.
     */
    public function salesForecast(): BelongsTo
    {
        // Assumes the SalesForecast model is in App\Models\SalesForecast
        return $this->belongsTo(SalesForecast::class, 'sales_forecasts_id');
    }

    /**
     * Get the personal (User) associated with this sales forecast entry.
     */
    public function personal(): BelongsTo
    {
        // Assumes the User model is in App\Models\User
        return $this->belongsTo(User::class, 'personal_id');
    }
}