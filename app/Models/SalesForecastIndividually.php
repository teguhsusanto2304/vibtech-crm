<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// ADDED: Import the related models used in the relationships
use App\Models\SalesForecast; 
use App\Models\Individual; 
use Illuminate\Database\Eloquent\Relations\Pivot;

class SalesForecastIndividually extends Pivot 
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_foreacast_individuallies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sales_forecasts_id',
        'individually_id',
        'data_status',
    ];
    
    // Note: If you want to use the pivot model directly in the controller, 
    // it's good practice to set $primaryKey and $incrementing to false, 
    // though Eloquent will often manage fine without it for simple pivots.

    // --- Relationships on the Pivot Model ---

    /**
     * Get the sales forecast that this individual assignment belongs to.
     */
    public function salesForecast(): BelongsTo
    {
        return $this->belongsTo(SalesForecast::class, 'sales_forecasts_id');
    }

    /**
     * Get the individual record associated with this sales forecast entry.
     */
    public function individual(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'individually_id');
    }

    /**
     * Get the personnel (users) assigned to this specific forecast item.
     */
    public function personalAssigned(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class, // The related model
            'sales_forecast_individual_personals', // The name of the pivot table
            'sf_individual_id', // The foreign key on the pivot table for THIS model (SalesForecastIndividual)
        );
    }
}