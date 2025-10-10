<?php

namespace App\Services;
use App\Models\SalesForecast;
use Illuminate\Validation\Rule;
use App\Models\SalesForecastIndividually;
use App\Models\SalesForecastIndividualValue;
use App\Models\SalesForecastPersonal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SalesForecastService
{
    public function reviewDetail($id)
    {
        
        $forecast = SalesForecast::find($id); // Assuming you fetch a single master forecast

        if ($forecast === null) {
             return redirect()
                ->route('v1.sales-forecast.create')
                ->with('success', 'Could you create your sales forecast first');
        } else {
            // The forecast object exists.
            $forecastId = $forecast->id;
        }

        $individuals = $forecast->individuals()
            ->where('sales_foreacast_individuallies.data_status', 1)
            ->with([])
            ->orderBy('individually_id','ASC')
            ->get();
        $groupedIndividuals = $individuals->groupBy('name');

        // 2. Fetch the existing forecast data efficiently
        // This assumes you have a way to link the sales forecast values back to the individuals
        // For simplicity, we'll assume a single SalesForecast record is being viewed/edited.
        
        // Find or create the master SalesForecast record (for headers)
        

        // Get all monthly values tied to this forecast record
        // This assumes the MonthlyForecastValue model exists (as described above)
        $forecastValues = SalesForecastIndividualValue::with(['salesForecastIndividual'])
            // Query the *parent* relationship records to filter by the master forecast ID
            ->whereHas('salesForecastIndividual', function ($query) use ($forecastId) {
                $query->where('sales_forecasts_id', $forecastId);
            })
            ->get()
            
            // 3. Generate the key using the individual ID from the loaded relationship 
            //    and the year/month from the current model.
            ->keyBy(function ($item) {
                return str_replace(' ','',strtolower($item->company)).'_'.$item->sf_individual_id.'_'.$item->sales_forecast_month;
            });


        // Generate the complex header structure (Months/Quarters)
        $quarters = [
            'Q1' => ['January', 'February', 'March'],
            'Q2' => ['April', 'May', 'June'],
            'Q3' => ['July', 'August', 'September'],
            'Q4' => ['October', 'November', 'December'],
            // Add Q4 if needed
        ];

        return view('sales.sales_forecast.result_review', compact('forecast','forecastId','groupedIndividuals', 'forecastValues', 'quarters'))->with('title', 'Sales Forecast Review')->with('breadcrumb', ['Home', 'Sales','Sales Forecast','Review']);
    }

    public function getSalesForecastData(Request $request)
    {
        $query = SalesForecast::with(['creator', 'personalAssigned'])
            ->withCount('individuals')
            ->withCount([
        'individuals as distinct_companies_count' => function ($q) {
            // Alias for the sales_foreacast_individuallies table
            $pivotTable = 'sales_foreacast_individuallies'; 
            
            // Alias for the sales_forecast_individual_values table
            $valuesTable = 'sales_forecast_individual_values'; 
            
            // Alias for the 'individuals' table, which is the table $q is currently querying (the pivot)
            $pivotAlias = $q->getModel()->getTable(); 

            // 1. Join the individuals pivot table (which $q is on) to the values table
            $q->leftJoin($valuesTable, 
                        "{$valuesTable}.sf_individual_id", '=', "{$pivotTable}.id") 
              
              // 2. Select the count of DISTINCT company IDs from the values table
              // Note: The 'company' column stores the company ID/name.
              ->selectRaw("COUNT(DISTINCT {$valuesTable}.company)");
        }
    ])
            ->whereHas('personalAssigned', function ($q) {
                // Filter the SalesForecasts to only include those 
                // where the current user is assigned.
                //$q->where('personal_id', auth()->user()->id);
                $q->where('personal_id', 2);
            });
            if ($request->filled('filter_year')) {
                $query->where('year', $request->input('filter_year'));
            }

        // 2. Use DataTables to process the query
        return DataTables::of($query)
            
            // 3. Add any custom columns (e.g., action buttons, formatted data)
            ->addColumn('action', function ($salesForecast) {
                // Example action buttons. Adjust as needed.
                return '<a href="'.route('v1.sales-forecast.review-detail', ['id' => $salesForecast->id]).'" class="btn btn-sm btn-info">View</a>';
            })
            
            // 4. Format the creator name
            ->addColumn('created_by_name', function ($salesForecast) {
                // Uses the eager-loaded 'creator' relationship
                return $salesForecast->creator->name ?? 'N/A'; 
            })

            // 5. Specify columns that are safe for searching/ordering 
            // This is crucial if you want to search/order on relationships.
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->input('search')['value'])) {
                    $searchValue = $request->input('search')['value'];
                    $query->where(function ($q) use ($searchValue) {
                        $q->where('year', 'like', "%{$searchValue}%");
                        // You can add more complex relationship searches here if needed
                    });
                }
            })
            ->addColumn('individuals_count', function ($salesForecast) {
                // Access the attribute added by withCount
                return $salesForecast->individuals_count; 
            })
            ->addColumn('distinct_companies_count', function ($salesForecast) {
                // Access the attribute added by withCount
                return floor($salesForecast->distinct_companies_count/12); 
            })

            // 6. Make the DataTables response
            ->rawColumns(['action','individuals_count','distinct_companies_count']) // Tell DataTables that the 'action' column contains HTML
            ->make(true);
    }

    public function salesForecastReviews(Request $request)
    {
        return view('sales.sales_forecast.list')->with('title', 'Sales Forecast Review')->with('breadcrumb', ['Home', 'Sales','Sales Forecast','Review']);
    }

    /**
     * Store a newly created sales forecast in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
        'year' => [
            'required',
            'integer',
            'min:' . date('Y'),
            'max:' . (date('Y') + 5),

            // This is the custom unique rule:
            // 1. Checks the 'sales_forecasts' table (adjust if your table name differs)
            // 2. Checks the 'year' column
            // 3. Applies a 'where' clause to ensure the 'created_by' field 
            //    matches the authenticated user's ID.
            Rule::unique('sales_forecasts', 'year')->where(function ($query) {
                // We don't need to use $request->year here because the value
                // of 'year' is already being checked by the 'unique' rule itself.
                return $query->where('created_by', auth()->user()->id);
            }),
        ],
        // Checkboxes send arrays, so we validate they exist and contain valid IDs
        'individually_ids' => ['required', 'array', 'min:1'],
        'individually_ids.*' => ['exists:individuallies,id'], // Ensure all IDs exist
        'personnel_ids' => ['required', 'array', 'min:1'],
        'personnel_ids.*' => ['exists:users,id'], // Ensure all IDs exist
    ]);

        // Wrap database operations in a transaction for atomicity
        DB::beginTransaction();
        try {
            // 2. Create the primary SalesForecast record
            $attr = [
                'year'       => $request->year,
                'currency'   => $request->currency,
                'created_by' => Auth::id(), // Get the ID of the currently authenticated user
                // 'data_status' will use the default of 1 defined in the model/migration
            ];
            $forecast = SalesForecast::where($attr)->first();
            if($forecast == NULL){                
                $forecast = SalesForecast::create($attr);
            }

            // 3. Attach relationships (using the BelongsToMany methods on the model)

            // 3a. Attach Individually Types (using the salesForecasts relationship on the Individual model or the inverse)
            // Assuming you have the 'individuals' relationship set up on the SalesForecast model
            $individuallyIds = array_unique($request->individually_ids);
            $syncData = [];

            // Structure the data for the sync method, setting the 'company' field for each ID
            foreach ($individuallyIds as $id) {
                // The key is the Individual ID, and the value is the array of pivot data
                $individually = [
                    'sales_forecasts_id' => $forecast->id,
                    'individually_id' => $id
                ];
                SalesForecastIndividually::create($individually);
            }

            

            // 3b. Attach Personnel (Users)
            // Assuming you have the 'personalAssigned' relationship set up on the SalesForecast model
            $personnelIds = array_unique($request->personnel_ids);
            
            foreach ($personnelIds as $id) {
                // The key is the Individual ID, and the value is the array of pivot data
                $personals = [
                    'sales_forecasts_id' => $forecast->id,
                    'personal_id' => $id
                ];
                SalesForecastPersonal::create($personals);
            }

            DB::commit();

            // Redirect with success message
            return redirect()
                ->route('v1.sales-forecast.list',['year'=>$request->year])
                ->with('success', 'Sales Forecast for year '.$request->year.' successfully created.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            \Log::error('Sales Forecast creation failed: ' . $e->getMessage());

            // Redirect back with an error message
            return back()
                ->withInput()
                ->with('error', 'An error occurred while creating the Sales Forecast. Please try again.'.$e->getMessage());
        }
    }
    
    public function showForecastGrid(Request $request)
    {
        // Define the period we are interested in
        $year = $request->input('year', date('Y'));
        
        $forecast = SalesForecast::where('year', $year)
            ->where('created_by', auth()->user()->id)
            ->first(); // Assuming you fetch a single master forecast

        if ($forecast === null) {
             return redirect()
                ->route('v1.sales-forecast.create')
                ->with('success', 'Could you create your sales forecast first');
        } else {
            // The forecast object exists.
            $forecastId = $forecast->id;
        }

        $individuals = $forecast->individuals()
            ->where('sales_foreacast_individuallies.data_status', 1)
            ->with([])
            ->orderBy('individually_id','ASC')
            ->get();
        $groupedIndividuals = $individuals->groupBy('name');

        // 2. Fetch the existing forecast data efficiently
        // This assumes you have a way to link the sales forecast values back to the individuals
        // For simplicity, we'll assume a single SalesForecast record is being viewed/edited.
        
        // Find or create the master SalesForecast record (for headers)
        $masterForecast = SalesForecast::firstOrCreate(
            ['year' => $year], 
            ['currency' => 'USD', 'created_by' => Auth::id()]
        );

        // Get all monthly values tied to this forecast record
        // This assumes the MonthlyForecastValue model exists (as described above)
        $forecastValues = SalesForecastIndividualValue::with(['salesForecastIndividual'])
            // Query the *parent* relationship records to filter by the master forecast ID
            ->whereHas('salesForecastIndividual', function ($query) use ($forecastId) {
                $query->where('sales_forecasts_id', $forecastId);
            })
            ->get()
            
            // 3. Generate the key using the individual ID from the loaded relationship 
            //    and the year/month from the current model.
            ->keyBy(function ($item) {
                return str_replace(' ','',strtolower($item->company)).'_'.$item->sf_individual_id.'_'.$item->sales_forecast_month;
            });


        // Generate the complex header structure (Months/Quarters)
        $quarters = [
            'Q1' => ['January', 'February', 'March'],
            'Q2' => ['April', 'May', 'June'],
            'Q3' => ['July', 'August', 'September'],
            'Q4' => ['October', 'November', 'December'],
            // Add Q4 if needed
        ];

        return view('sales.sales_forecast.result', compact('forecast','forecastId','groupedIndividuals', 'forecastValues', 'quarters', 'year'))->with('title', 'Sales Forecast')->with('breadcrumb', ['Home', 'Sales','Sales Forecast','Create']);
    }

    
    /**
     * Handles the POST request to save the individual sales forecast data.
     * Route: POST /v1/sales-forecast/save
     */
    public function save(Request $request)
    {
        // 1. Validation (Highly Recommended)
        $validatedData = $request->validate([
            'year' => 'required|integer',
            // 'forecast' is an array of arrays: [individual_id][key_month][amount]
            'forecast' => 'required|array', 
            'forecast.*.*.amount' => 'nullable|numeric|min:0',
            // Assuming currency is also passed, though not in the loop in this snippet
            // 'forecast.*.*.currency' => 'required|string', 
        ]);

        $forecastData = $validatedData['forecast'];
        DB::beginTransaction();

        try {
            
            // 1. Loop through each Individual ID (The key here is $individualId)
            foreach ($forecastData as $individualId => $monthsData) {
                
                // 2. Loop through each Month's Data for that individual (The key here is $monthDateCode)
                foreach ($monthsData as $monthDateCode => $data) {
                    
                    // --- Value Extraction ---
                    $amount = (float) $data['amount'] ?? 0.00;
                    
                    // The sfIndividualId comes from the outer loop key
                    $sfIndividualId = (int) $individualId; 

                    // Parse $monthDateCode (e.g., 'nadihealth_10')
                    $parts = explode('_', $monthDateCode);
                    //dd($monthDateCode);
                    
                    // Check if explosion was successful and month is available
                    if (count($parts) < 2) {
                        \Log::warning("Skipping invalid monthDateCode format: " . $monthDateCode);
                        continue;
                    }

                    // The last part is the month
                    $month = (int) array_pop($parts); 
                    // The rest is the company key/name (in case it had underscores)
                    $company = implode('_', $parts); 

                    // --- Custom Filter Preparation ---
                    // Calculate the slug that the DB filter will generate (REPLACE(LOWER(company), ' ', ''))
                    $companySlug = str_replace(' ', '', strtolower(str_replace("_".$month, "",$company)));

                    // --- Define Search Attributes (The "WHERE" clause parts for exact match) ---
                    $searchAttributes = [
                        'sf_individual_id'      => $sfIndividualId,
                        'sales_forecast_year'   => $request->year,
                        'sales_forecast_month'  => $month,
                        'data_type'             => 'monthly_total', 
                    ];
                    
                    // --- Define Values to be Updated/Inserted ---
                    $newValues = [
                        // 'sales_forecast_currency' => $data['currency'], // Uncomment if you are using this field
                        'amount'                    => $amount,
                    ];
                    
                    // Only process records with a positive amount or to clear a forecast
                    if ($amount >= 0) {
                        // A. Start the query with exact conditions
                        $query = SalesForecastIndividualValue::where($searchAttributes);

                        // B. Apply the custom, transformed lookup for the 'company' field using whereRaw
                        $existingValue = SalesForecastIndividualValue::whereRaw("concat(REPLACE(LOWER(company), ' ', ''),'_',sf_individual_id,'_',sales_forecast_month) = ?", [$monthDateCode])
                            ->first();

                        //dd($existingValue);
                                               
                        // C. Check and perform the action
                        if ($existingValue) {
                            // Record found: UPDATE it
                            $existingValue->update($newValues);
                        } 
                    }
                    
                    // Note: If you want to DELETE records where $amount is 0,
                    // you would add an 'else if ($amount === 0) { ... delete logic ... }' block here.

                }
            }
            DB::commit(); // Commit all changes at the end of the entire process

            return redirect()->back()->with('success', 'Sales forecast saved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Sales Forecast Save Error: ' . $e->getMessage()); 
            return redirect()->back()->withInput()->with('error', 'An error occurred while saving the forecast: ' . $e->getMessage());
        }
    }

    
    /**
     * Store a new variable (Individual) associated with a Sales Forecast.
     * Maps to the 'v1.sales-forecast.store-variable' route.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeVariable(Request $request)
    {
        // 1. Validation (Returns 422 JSON response automatically on failure)
        $validatedData = $request->validate([
            'sales_forecasts_id' => 'required|integer|exists:sales_forecasts,id',
            'individually_id' => 'required|integer', // Added integer|exists check
        ]);

        $forecastId = $validatedData['sales_forecasts_id'];
        $individualId = $validatedData['individually_id'];

        try {
            // 2. Prevent Duplicates (Uncommented and fixed for JSON response)
            $exists = SalesForecastIndividually::where('sales_forecasts_id', $forecastId)
                ->where('individually_id', $individualId)
                ->exists();

            if ($exists) {
                // Return a 409 Conflict error for duplicates
                return response()->json([
                    'message' => 'Conflict: This variable is already linked to the current sales forecast.'
                ], 409);
            }

            // 3. Create the new pivot table entry
            $newVariable = SalesForecastIndividually::create([
                'sales_forecasts_id' => $forecastId,
                'individually_id' => $individualId,
                'data_status' => 1, // Using integer 1 for status
            ]);
            
            // Optionally load the related model for the response
            $newVariable->load('individual');

            // 4. Return success JSON response (201 Created)
            return response()->json([
                'message' => 'Variable successfully added to the forecast!',
                'data' => $newVariable,
            ], 201);

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            \Log::error('API Error adding variable to sales forecast: ' . $e->getMessage());

            // Return a 500 Internal Server Error
            return response()->json([
                'message' => 'Server Error: Could not save the variable.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new Company associated with a specific Individual Variable (Pivot).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCompany(Request $request)
    {
        // 1. Validation (Returns 422 JSON response automatically on failure)
        $validatedData = $request->validate([
            // Ensure IDs are present and exist in their respective tables
            'sf_individual_id' => 'required',
            'company' => 'required', // Assuming you have a companies table
            'sales_forecast_currency' => 'required|string|max:3|in:SGD,MYR,USD', // Validate currency format
        ]);

        $salesForecastIndividualId = $validatedData['sf_individual_id'];
        $company = $validatedData['company'];
        $currency = $validatedData['sales_forecast_currency'];
        $salesForecastIndividual = SalesForecastIndividually::find($salesForecastIndividualId);
        if ($salesForecastIndividual) {
            $forecastYear = $salesForecastIndividual->salesForecast->year; 
        }

        try {
            // 2. Prevent Duplicates
            // Check if this specific company is already linked to this specific individual variable in this forecast
            $exists = SalesForecastIndividualValue::where('sf_individual_id', $salesForecastIndividualId)
                ->where('company', $company)
                ->where('sales_forecast_currency', $currency)
                ->where('sales_forecast_year', $forecastYear)
                ->exists();

            if ($exists) {
                // Return a 409 Conflict error for duplicates
                return response()->json([
                    'message' => 'Conflict: This company is already linked to this variable in the forecast.'
                ], 409); 
            }

            // 3. Create the new pivot table entry
            for($i=1; $i<=12; $i++){

                $newCompanyLink = SalesForecastIndividualValue::create([
                    'sf_individual_id' => $salesForecastIndividualId,
                    'company' => $company,
                    'sales_forecast_currency' => $currency,
                    'sales_forecast_year' => $forecastYear,
                    'sales_forecast_month' => $i, // Default status for the new link
                ]);

            }
            
            
            // Optionally load the related model (Company) to return useful data
            //$newCompanyLink->load('company'); 

            // 4. Return success JSON response (201 Created)
            return response()->json([
                'message' => 'Company successfully linked to the variable!',
                'data' => $newCompanyLink,
            ], 201);

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            \Log::error('API Error adding company to sales forecast variable: ' . $e->getMessage());

            // Return a 500 Internal Server Error
            return response()->json([
                'message' => 'Server Error: Could not link the company to the variable.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}