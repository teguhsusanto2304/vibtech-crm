<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesForecastIndividualValue extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_forecast_individual_values';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sf_individual_id',
        'sales_forecast_month',
        'sales_forecast_year',
        'company',
        'sales_forecast_currency',
        'amount',
        'data_type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * Note: You may want to cast 'amount' to 'float' or 'decimal'
     * if you need it as a numeric type immediately after retrieval.
     *
     * @var array
     */
    protected $casts = [
        'sales_forecast_month' => 'integer',
        'sales_forecast_year' => 'integer',
        'amount' => 'decimal:2', // Casts the amount as a decimal with 2 places
    ];

    // --- Relationships ---

    /**
     * Get the SalesForecastIndividual record that this value belongs to.
     *
     * The foreign key is 'sf_individual_id' referencing the 'sales_foreacast_individuallies' table.
     */
    public function salesForecastIndividual(): BelongsTo
    {
        // We must explicitly specify the foreign key ('sf_individual_id') 
        // since it doesn't follow Laravel's default convention (sales_forecast_individual_id).
        return $this->belongsTo(SalesForecastIndividually::class, 'sf_individual_id');
    }
}