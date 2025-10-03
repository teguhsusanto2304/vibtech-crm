<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Individual extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'individuallies'; 


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'data_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        // 'data_status' => 'integer', // Optional, as Eloquent handles integers correctly
    ];

    // --- Relationships (If applicable, add the pivot relationship here) ---

    /**
     * The sales forecasts this Individual is assigned to (via a pivot table).
     */
    public function salesForecasts(): BelongsToMany
    {
        // Assumes your pivot table model is SalesForecastIndividual and the SalesForecast model exists
        return $this->belongsToMany(
            SalesForecast::class,
            'sales_foreacast_individuallies', // The pivot table name
            'individually_id',               // FK on the pivot table for Individual
            'sales_forecasts_id'             // FK on the pivot table for SalesForecast
        )
        ->withPivot('data_status')
        ->as('assignment');
    }
}