<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesForecastController extends Controller
{
    public function index()
    {
        return view('sales.sales_forecast.index')->with('title', 'Sales Forecast')->with('breadcrumb', ['Home', 'Sales','Sales Forecast']);
    }

    public function create()
    {
        $claimTypes = [
        (object)[
            'id' => 1,
            'name' => 'Artesis'
        ],
        (object)[
            'id' => 2,
            'name' => 'PdMA'
        ],
        (object)[
            'id' => 3,
            'name' => 'SDT Ultrasound'
        ],
        (object)[
            'id' => 4,
            'name' => 'Easy-Laser'
        ],
        (object)[
            'id' => 5,
            'name' => 'Crystal Instrument'
        ],
        (object)[
            'id' => 6,
            'name' => 'VMS'
        ],
        (object)[
            'id' => 7,
            'name' => 'Spare Parts & Components'
        ],
        (object)[
            'id' => 8,
            'name' => 'MSI'
        ],
        (object)[
            'id' => 9,
            'name' => 'Service'
        ],
        (object)[
            'id' => 10,
            'name' => 'Project'
        ],
        (object)[
            'id' => 11,
            'name' => 'Training'
        ],
        (object)[
            'id' => 12,
            'name' => 'Maintenance Contract'
        ],
    ];
        return view('sales.sales_forecast.form',compact('claimTypes'))->with('title', 'Sales Forecast')->with('breadcrumb', ['Home', 'Sales','Sales Forecast','Create']);
    }

    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'claim_type_id' => 'required|integer|min:1',
            'forecast_year' => 'required|integer|min:' . (date('Y')),
        ]);

        $claimTypeId = $request->input('claim_type_id');
        $forecastYear = $request->input('forecast_year');
        
        // --- 2. Mock Historical Data and Trend Projection ---
        // In a real app, you would fetch historical sales data from the database
        // based on $claimTypeId.

        // Example: Artesis (ID 1) Historical Data (for 2022, 2023, 2024)
        $history = [
            2022 => 120000,
            2023 => 140000,
            2024 => 165000,
        ];

        // Simple Trend Projection: Assume a linear growth of $25,000 per year
        $latestYear = 2024;
        $latestSales = $history[$latestYear];
        $growthRate = 25000; // Mock average annual growth

        $yearsDifference = $forecastYear - $latestYear;
        
        $forecastedSales = $latestSales + ($growthRate * $yearsDifference);
        
        // --- 3. Prepare Display Data ---
        
        // Find the selected claim type name for display
        $claimTypeName = collect($this->create()['claimTypes'])->firstWhere('id', $claimTypeId)->name ?? 'Unknown Type';
        
        return view('sales.sales_forecast.result', [
            'claimTypeName' => $claimTypeName,
            'forecastYear' => $forecastYear,
            'forecastedSales' => number_format($forecastedSales, 2, '.', ','),
            'calculationDetails' => 'This forecast used a simple linear trend projection based on mock historical data showing $25,000 annual growth.'
        ])->with('title', 'Sales Forecast')->with('breadcrumb', ['Home', 'Sales','Sales Forecast','Result']);
    }

}
