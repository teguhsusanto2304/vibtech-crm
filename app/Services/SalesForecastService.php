<?php

namespace App\Services;
use App\Models\SalesForecast;
use App\Models\Individual;
use App\Models\SalesForecastIndividually;
use App\Models\SalesForecastIndividualValue;
use App\Models\SalesForecastPersonal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesForecastService
{

    /**
     * Store a newly created sales forecast in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validation (Validation looks correct for all necessary fields)
        $request->validate([
            'year'              => ['required', 'integer', 'min:' . date('Y'), 'max:' . (date('Y') + 5)],
            'currency'          => ['required', 'string', 'max:3'],
            'company'           => ['required', 'string', 'max:255'],
            'individually_ids'  => ['required', 'array', 'min:1'],
            'individually_ids.*' => ['exists:individuallies,id'],
            'personnel_ids'     => ['required', 'array', 'min:1'],
            'personnel_ids.*'   => ['exists:users,id'],
        ]);

        DB::beginTransaction();
        try {
            // 2. Create or Retrieve the primary SalesForecast record
            $attr = [
                'year'       => $request->year,
                'currency'   => $request->currency, // Changed from hardcoded 'SGD'
                'created_by' => Auth::id(),
            ];
            $forecast = SalesForecast::firstOrCreate($attr); // Cleaner way to handle first() or create()

            // 3. Attach relationships

            // 3a. Attach Individually Types to SalesForecast
            $individuallyIds = array_unique($request->individually_ids);
            $syncData = [];

            foreach ($individuallyIds as $individualId) {
                $syncData[$individualId] = [
                    'company' => $request->company,
                    'currency' => $request->currency
                ];
            }
            
            // This attaches or updates the 'sales_foreacast_individuallies' records.
            // It returns an array containing IDs of attached/updated records.
            $syncResult = $forecast->individuals()->syncWithoutDetaching($syncData);

            // -----------------------------------------------------------------------
            // 3b. Attach Personnel to the newly created/updated SalesForecastIndividuals
            // -----------------------------------------------------------------------
            
            $syncedIndividualIds = array_merge($syncResult['attached'], $syncResult['updated']);

            $sfIndividuals = SalesForecastIndividually::whereIn('individually_id', $syncedIndividualIds)
                                                ->where('sales_forecasts_id', $forecast->id)
                                                ->get();

            $personnelIds = array_unique($request->personnel_ids);

            // Loop through each relevant SalesForecastIndividual record
            foreach ($sfIndividuals as $sfIndividual) {
                $sfIndividual->personalAssigned()->syncWithoutDetaching($personnelIds);
            }

            DB::commit();

            return redirect()
                ->route('v1.sales-forecast.list')
                ->with('success', 'Sales Forecast successfully created and personnel assigned.');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Sales Forecast creation failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while creating the Sales Forecast: ' . $e->getMessage());
        }
    }
    /**
     * Store a newly created sales forecast in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeOld(Request $request)
    {
        // 1. Validation
        $request->validate([
            'year'              => ['required', 'integer', 'min:' . date('Y'), 'max:' . (date('Y') + 5)],
            'currency'          => ['required', 'string', 'max:3'],
            'company'           => ['required', 'string', 'max:255'],
            // Checkboxes send arrays, so we validate they exist and contain valid IDs
            'individually_ids'  => ['required', 'array', 'min:1'],
            'individually_ids.*' => ['exists:individuallies,id'], // Ensure all IDs exist
            'personnel_ids'     => ['required', 'array', 'min:1'],
            'personnel_ids.*'   => ['exists:users,id'],          // Ensure all IDs exist
        ]);

        // Wrap database operations in a transaction for atomicity
        DB::beginTransaction();
        try {
            // 2. Create the primary SalesForecast record
            $attr = [
                'year'       => $request->year,
                'currency'   => 'SGD',
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
                $syncData[$id] = [
                    'company' => $request->company,
                    'currency' => $request->currency
                ];
            }

            // Attach Individually Types with the extra pivot data
            //$forecast->individuals()->sync($syncData);  //this code delete old data
            $forecast->individuals()->syncWithoutDetaching($syncData);  //this code keeped old data

            // 3b. Attach Personnel (Users)
            // Assuming you have the 'personalAssigned' relationship set up on the SalesForecast model
            $personnelIds = array_unique($request->personnel_ids);
            
            // The sync method is ideal here too
            //$forecast->personalAssigned()->sync($personnelIds); //deleted old data
            $forecast->personalAssigned()->syncWithoutDetaching($personnelIds);

            DB::commit();

            // Redirect with success message
            return redirect()
                ->route('v1.sales-forecast.list')
                ->with('success', 'Sales Forecast for ' . $forecast->company . ' successfully created.');

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
            ->with([
        // Assuming 'pivot' refers to the SalesForecastIndividual model instance
        // You need to ensure your 'individuals()' relationship uses ->using() to 
        // return the pivot data as a Model instance, or adjust the query below.
        
        // The most direct way is to eager load the personnel on the pivot object itself.
        // Eloquent often requires specific setup for this. The cleaner approach is below:

        // Load the personnel assigned to each pivot record.
        'pivot.personalAssigned' => function ($query) {
             // You can select only the name if desired
             $query->select('users.id', 'users.name'); 
        }
    ])
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
                return $item->sf_individual_id.'_'.$item->sales_forecast_month;
            });


        // Generate the complex header structure (Months/Quarters)
        $quarters = [
            'Q1' => ['January', 'February', 'March'],
            'Q2' => ['April', 'May', 'June'],
            'Q3' => ['July', 'August', 'September'],
            'Q4' => ['October', 'November', 'December'],
            // Add Q4 if needed
        ];

        return view('sales.sales_forecast.result', compact('groupedIndividuals', 'forecastValues', 'quarters', 'year'))->with('title', 'Sales Forecast')->with('breadcrumb', ['Home', 'Sales','Sales Forecast','Create']);
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
            // 'forecast' is an array of arrays: [individual_id][month_date][amount]
            'forecast' => 'required|array', 
            'forecast.*.*.amount' => 'nullable|numeric|min:0',
        ]);
        $forecastData = $validatedData['forecast'];
        DB::beginTransaction();

        try {

            // 1. Loop through each Individual ID (The key here is $individualId)
            foreach ($forecastData as $individualId => $monthsData) {
                
                // 2. Loop through each Month's Data for that individual (The key here is $monthDateCode)
                foreach ($monthsData as $monthDateCode => $data) {
                    
                    // --- Access the amount here ---
                    $amount = (float) $data['amount'] ?? 0.00;
                    $explodedKey = explode('_',$monthDateCode);

                        $sfIndividualId = (int) $explodedKey[0];    // Index 1, or whichever holds the pivot ID
                        $year = (int) $explodedKey[2];
                        $month = (int) $explodedKey[3];

                        // 1. Define the unique attributes to SEARCH for (The "WHERE" clause)
                        $attributes = [
                            'sf_individual_id' => $sfIndividualId,
                            'sales_forecast_year' => $year,
                            'sales_forecast_month' => $month,
                            'data_type' => 'monthly_total', // Match the default type
                        ];
                        $values = [
                            'amount' => $amount,
                        ];

                        // Use updateOrCreate() to handle both cases efficiently
                        if($amount>0){
                            SalesForecastIndividualValue::updateOrCreate($attributes, $values);
                        }
                        
                }
            }
            DB::commit(); // Commit all changes at the end of the entire process

            return redirect()->back()->with('success', 'Sales forecast saved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Sales Forecast Save Error: ' . $e->getMessage()); 
            return redirect()->back()->withInput()->with('error', 'An error occurred while saving the forecast.');
        }
        
        
    }
}