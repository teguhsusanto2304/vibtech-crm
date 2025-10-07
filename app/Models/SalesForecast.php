<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SalesForecast extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_forecasts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'year',
        'currency',
        'created_by',
        'data_status',
    ];

    /**
     * Get the user that created the sales forecast.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function personalAssigned(): BelongsToMany // <-- This is the missing method
    {
        return $this->belongsToMany(
            User::class,                     // 1. The related model
            'sales_forecast_personals',     // 2. The pivot table name (from your migration)
            'sales_forecasts_id',            // 3. Foreign key on the pivot table for THIS model (SalesForecast)
            'personal_id'                    // 4. Foreign key on the pivot table for the RELATED model (User)
        )
        // Include any pivot columns needed for storage/retrieval
        ->withPivot('data_status','personal_id'); 
    }


    public function individuals()
    {
        return $this->belongsToMany(
            Individual::class,
            'sales_foreacast_individuallies',
            'sales_forecasts_id',
            'individually_id'
        )
        ->using(SalesForecastIndividually::class) // <-- Use the custom pivot model
        ->withPivot('individually_id','id'); // <-- Make the 'company' field available
    }
}
