@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .claim-type-group {
            display: grid;
            grid-template-columns: 1fr; /* Default to single column on small screens */
            gap: 1rem 2rem; /* Row gap, column gap */
            margin-bottom: 2rem;
        }
        @media (min-width: 768px) { /* md breakpoint */
            .claim-type-group {
                grid-template-columns: repeat(2, 1fr); /* Force exactly 2 equal columns on medium screens and up */
            }
        }

                        </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- custom-icon Breadcrumb-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom-icon">
                    @foreach ($breadcrumb as $item)
                        <li class="breadcrumb-item">
                            @if($item == 'Job Assignment Form')
                                <a href="{{ route('v1.job-assignment-form')}}">{{ $item }}</a>
                            @else
                                <a href="javascript:void(0);">{{ $item }}</a>
                            @endif
                            <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>

        <h3>{{ $title }}</h3>
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Oops! Something went wrong:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
         @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <style>
    <style>
        /* CRITICAL FIX: Forces the browser to honor explicit column widths */
        table { 
            border-collapse: collapse; 
            font-family: 'Inter', Arial, sans-serif; 
            table-layout: fixed; 
            width: 100%; /* Ensure the table fills its container */
        }
        th, td { 
            border: 1px solid #ccc; 
            padding: 8px 4px; 
            text-align: center; 
            overflow: hidden; /* Prevent content overflow from expanding cells */
            text-overflow: ellipsis; /* Optional: Show '...' if content is cut off */
            white-space: nowrap; /* Optional: Prevent wrapping in narrow cells */
        }
        thead th { 
            background-color: #e5e7eb; /* Light gray */
            font-weight: bold; 
        }
        .variable-col { 
            text-align: left; 
            background-color: #f8f9fa; 
        }
        .variable-col-header {
            width: 500px; /* Explicit width will now be honored */
        }
        .input-field { 
            width: 100%; 
            height: 30px;
            text-align: right; 
            border: none; 
            padding: 2px;
            box-sizing: border-box;
        }
        .table-responsive-scroll {
            overflow-x: auto; 
            max-width: 100%;
        }
    </style>
<div class="table-responsive-scroll"> 
<form method="GET" action="{{ route('v1.sales-forecast.list') }}">
    @php
    // --- MOCK DATA FOR NEW HEADER INFO (Replace these with actual data passed from your controller) ---
    // Example: $salesForecastModel->creator->name
    $forecastCreatorName = $forecast->creator->name; 
    // Example: $forecast->created_at->format('Y-m-d')
    $createdAt = $forecast->created_at->format('d-m-Y');
    // Example: $salesForecastModel->viewers->pluck('name')->toArray()
    $viewerList = $forecast->personalAssigned->pluck('name')->toArray();
    // -------------------------------------------------------------------------------------------------
@endphp
    <!-- Sales Forecast Header Information -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card p-4 shadow-sm" style="background-color: #f7f7f7;">
                    <div class="row">
                        <!-- Sales Forecast Year -->
                        <div class="col-md-4 mb-3 mb-md-0">
                            <p class="mb-1 text-muted small"><i class="fas fa-calendar-alt me-2 text-info"></i>SALES FORECAST YEAR</p>
                            <h4 class="text-primary font-bold">{{ $year }}</h4>
                        </div>
                        
                        <!-- Created by -->
                        <div class="col-md-4 mb-3 mb-md-0 border-start border-light-2">
                            <p class="mb-1 text-muted small"><i class="fas fa-user-tie me-2 text-success"></i>CREATED BY</p>
                            <h5 class="text-dark">{{ $forecastCreatorName }}</h5>
                            <small class="text-secondary">Created on: {{ $createdAt }}</small>
                        </div>
                        
                        <!-- Personnels as Viewers -->
                        <div class="col-md-4 border-start border-light-2">
                            <p class="mb-2 text-muted small"><i class="fas fa-users me-2 text-warning"></i>VIEWERS ({{ count($viewerList) }})</p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($viewerList as $viewer)
                                    <span class="badge bg-secondary rounded-pill shadow-sm">{{ $viewer }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Sales Forecast Header Information -->
    
    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="projectStartDate" class="form-label">Forecast Year</label>
                            <input type="number" name="year" id="year" class="form-control" min="{{ date('Y')-5 }}" max="{{ date('Y')+5 }}" value="{{ $year }}">
                        </div>
                        <div class="col-md-2">
                            <label for="projectStartDate" class="form-label invisible">Create Forecast Year</label>
                            <button type="submit" class="btn btn-info">Search</a>
                        </div>
                        
    </div>
</form>
<form method="POST" action="{{ route('v1.sales-forecast.save') }}">
    <input type="hidden" name="year" value="{{ $year }}">
    @csrf
    
    <table>
        <thead>
            <tr>
                <th rowspan="2" class="variable-col variable-col-header">Variable</th>
                <th rowspan="2" class="variable-col variable-col-header"><button type="button" class="btn btn-sm btn-primary ms-2"
                        data-bs-toggle="modal" data-bs-target="#addVariableModal">
                    Add Variable
                </button>
                
            </th>
                @foreach ($quarters as $qName => $months)
                    {{-- Colspan is now the number of months + 1 for the Quarter Total --}}
                    <th colspan="{{ count($months) + 1 }}">{{ $qName }}</th>
                @endforeach
                <th rowspan="3">Grand Total</th>
            </tr>
            
            <tr>
                @foreach ($quarters as $qName => $months)
                    @php $monthWidth = '80px'; // Define your desired width @endphp
                    @foreach ($months as $monthName)
                        <th style="width: {{ $monthWidth }}; min-width: {{ $monthWidth }};">{{ $monthName }}</th> {{-- Set width for month column --}}
                    @endforeach
                    <th style="width: 100px; min-width: 100px;">{{ $qName }} Total</th> {{-- Set a slightly wider width for the quarter total --}}
                @endforeach
            </tr>
            
            <tr>
                @foreach ($quarters as $qName => $months)
                    @foreach ($months as $monthName)
                         {{-- Only one column header per month --}}
                    @endforeach
                    
                @endforeach
            </tr>
        </thead>
        
        <tbody>
    {{-- Initialize Grand Totals for the entire table (Column Summaries) --}}
    @php
        // Grand Table Totals (SPLIT BY CURRENCY) 💰
        $tableMonthTotals_SGD = [];
        $tableMonthTotals_MYR = [];
        $tableQuarterTotals_SGD = [];
        $tableQuarterTotals_MYR = [];
        $tableGrandTotal_SGD = 0;
        $tableGrandTotal_MYR = 0;
        
        $iMonthCounter = 0;
        
        // Initialize arrays with 0 for both currencies
        foreach ($quarters as $qName => $months) {
            $tableQuarterTotals_SGD[$qName] = 0;
            $tableQuarterTotals_MYR[$qName] = 0;
            foreach ($months as $monthName) {
                $iMonthCounter++;
                $tableMonthTotals_SGD[$iMonthCounter] = 0;
                $tableMonthTotals_MYR[$iMonthCounter] = 0;
            }
        }
    @endphp

    @foreach ($groupedIndividuals as $individualName => $individualsInGroup)
        
        {{-- Initialize Totals for the current Variable Group (SPLIT BY CURRENCY) --}}
        @php
            $rowspanCount = $individualsInGroup->count(); 
            $firstRow = true; 
            
            // Variable Group Totals (SPLIT BY CURRENCY)
            $variableMonthTotals_SGD = [];
            $variableMonthTotals_MYR = [];
            $variableQuarterTotals_SGD = [];
            $variableQuarterTotals_MYR = [];
            
            // Initialize arrays for the Variable Group totals
            foreach ($quarters as $qName => $months) {
                $variableQuarterTotals_SGD[$qName] = 0;
                $variableQuarterTotals_MYR[$qName] = 0;
                foreach ($months as $monthName) {
                    $variableMonthTotals_SGD[] = 0;
                    $variableMonthTotals_MYR[] = 0;
                }
            }
        @endphp

        {{-- Loop through the Companies/Individuals within the current Variable Group --}}
        @foreach ($individualsInGroup as $individual)
            @php 
                $individualRowTotal = 0;

                $currency = $forecastValues->get($individual->pivot->id.'_1')->sales_forecast_currency ?? 'N/A';
                
            @endphp
            
            <tr>
                {{-- ... (Your Variable Name Column remains the same, rowspan will need to be +2 or more now) ... --}}
                @if ($firstRow)
                    {{-- Change rowspan to $rowspanCount + 2 if you add the currency summary row for the group --}}
                    <td class="variable-col" rowspan="{{ $rowspanCount  }}" style="font-weight: bold;" colspan="2">
                        {{ $individualName }}
                    </td>
                    <td class="variable-col" rowspan="{{ $rowspanCount  }}" colspan="17">
                        <button type="button" class="btn btn-sm btn-secondary ms-2"
                            data-bs-toggle="modal" 
                            data-bs-target="#addCompanyModal"
                            data-individual-id="{{ $individual->id }}"
                            data-sf-individual-id="{{ $individual->pivot->id }}"
                            data-individual-name="{{ $individualName }}">
                            Add Company
                        </button>
                    </td>
                    @php $firstRow = false; @endphp
                @endif
            </tr>
            @php
                $sfIndividualValues = \App\Models\SalesForecastIndividualValue::where('sf_individual_id', $individual->pivot->id)
                                    // Select only the 'company' column
                                    ->select('company')
                                    // Ensure only distinct values of 'company' are returned
                                    ->distinct('company') 
                                    // Execute the query and pluck the values into a simple array of strings
                                    ->pluck('company'); 
                    
                                    
            @endphp
            @foreach ($sfIndividualValues as $companyName)
            @php
                $key = str_replace(' ','',strtolower($companyName)).'_'.$individual->pivot->id;
                $sf_currency = $forecastValues->get($key.'_1')->sales_forecast_currency ?? 'N/A';
                $iMonth = 0;
            @endphp
            <tr>              
                {{-- 2. COMPANY NAME COLUMN --}}
                <td colspan="2">{{ $forecastValues->get($key.'_1')->company ?? 'N/A' }} 
                    <p><span class="badge badge-sm {{ $sf_currency=='SGD'? 'bg-info':'bg-warning' }}">{{ $sf_currency }}</span></p></td>
                
                {{-- 3. MONTHLY FORECAST COLUMNS (Accumulation Logic) --}}
                @foreach ($quarters as $qName => $months)
                    @php 
                        $quarterTotal = 0;
                    @endphp
                    @foreach ($months as $monthIndex => $monthName)
                        @php
                            $iMonth++; 
                            $monthDate = date('Y_m', strtotime($monthName));
                            //$key = $individual->pivot->id.'_'.$iMonth;
                            $savedValue = $forecastValues->get($key.'_'.$iMonth)->amount ?? 0;
                            
                            $quarterTotal += $savedValue;
                            $individualRowTotal += $savedValue;
                            
                            // 💡 ACCUMULATION SPLIT BY CURRENCY
                            if ($sf_currency == 'SGD') {
                                $variableMonthTotals_SGD[$iMonth - 1] += $savedValue; // Variable Group SGD Total
                                $tableMonthTotals_SGD[$iMonth] += $savedValue;       // Table Grand SGD Total
                            } elseif ($sf_currency == 'MYR') {
                                $variableMonthTotals_MYR[$iMonth - 1] += $savedValue; // Variable Group MYR Total
                                $tableMonthTotals_MYR[$iMonth] += $savedValue;       // Table Grand MYR Total
                            }
                        @endphp
                        {{-- ... (Your Input Field HTML remains the same) ... --}}
                        <td>
                            <input 
                                type="number" 
                                class="input-field" 
                                name="forecast[{{ $individual->pivot->id }}][{{ $key.'_'.$iMonth }}][amount]" 
                                value="{{ number_format($savedValue, 2, '.', '') }}" 
                                step="0.01" 
                            >
                        </td>
                    @endforeach
                    
                    {{-- Q Total for the current Company Row --}}
                    <td data-quarter-total="{{ $qName }}" style="text-align: right; padding-right: 5px;"><strong style="color: #f51313ff;">{{ number_format($quarterTotal, 2) }}</strong></td>
                    
                    @php 
                        // 💡 ACCUMULATION SPLIT BY CURRENCY for Quarters
                        if ($currency == 'SGD') {
                            $variableQuarterTotals_SGD[$qName] += $quarterTotal;
                            $tableQuarterTotals_SGD[$qName] += $quarterTotal; 
                        } elseif ($currency == 'MYR') {
                            $variableQuarterTotals_MYR[$qName] += $quarterTotal;
                            $tableQuarterTotals_MYR[$qName] += $quarterTotal; 
                        }
                    @endphp
                @endforeach
                
                {{-- Grand Total for the current Company Row --}}
                <td data-grand-total style="text-align: right; padding-right: 5px;"><strong style="color: #480ee8ff;">{{ number_format($individualRowTotal, 2) }}</strong></td>
            </tr>
        @endforeach
        
        {{-----------------------------------------------------------}}
        {{-- VARIABLE GROUP SUMMARY ROW (SPLIT BY CURRENCY) --}}
        {{-----------------------------------------------------------}}
        @php
            // Calculate Grand Total for the variable group
            $variableGrandTotal_SGD = array_sum($variableMonthTotals_SGD);
            $variableGrandTotal_MYR = array_sum($variableMonthTotals_MYR);
            $iMonthCounter = 0; // Reset counter
        @endphp
        
        @if ($variableGrandTotal_SGD > 0 || $variableGrandTotal_MYR > 0)
            <tr style="background-color: #e6e6fa; font-weight: bold;">
                <td rowspan="2">Total {{ $individualName }}</td>
                
                {{-- SGD Row --}}
                <td>SGD</td>
                @foreach ($quarters as $qName => $months)
                    @foreach ($months as $monthName)
                        <td style="text-align: right; padding-right: 5px;">{{ number_format($variableMonthTotals_SGD[$iMonthCounter++], 2) }}</td>
                    @endforeach
                    <td style="text-align: right; padding-right: 5px;">{{ number_format($variableQuarterTotals_SGD[$qName], 2) }}</td>
                @endforeach
                <td style="text-align: right; padding-right: 5px;">{{ number_format($variableGrandTotal_SGD, 2) }}</td>
            </tr>
            
            {{-- Reset month counter for MYR row --}}
            @php $iMonthCounter = 0; @endphp 
            
            <tr style="background-color: #e6e6fa; font-weight: bold;">
                {{-- Variable Name column is merged above, only need Company column --}}
                <td>MYR</td>
                @foreach ($quarters as $qName => $months)
                    @foreach ($months as $monthName)
                        <td style="text-align: right; padding-right: 5px;">{{ number_format($variableMonthTotals_MYR[$iMonthCounter++], 2) }}</td>
                    @endforeach
                    <td style="text-align: right; padding-right: 5px;">{{ number_format($variableQuarterTotals_MYR[$qName], 2) }}</td>
                @endforeach
                <td style="text-align: right; padding-right: 5px;">{{ number_format($variableGrandTotal_MYR, 2) }}</td>
            </tr>
        @endif
      @endforeach
    @endforeach

    {{-----------------------------------------------------------}}
    {{-- FINAL TABLE GRAND SUMMARY ROW (SPLIT BY CURRENCY) --}}
    {{-----------------------------------------------------------}}
    @php
        $iMonthCounter = 0; // Reset counter
    @endphp
    
    <tr style="background-color: #dcdcdc; font-weight: bold; border-top: 3px double #333;">
        <td  rowspan="2">GRAND TOTAL</td>
        <td  >SGD</td>      
        
        {{-- SGD Row --}}
        @foreach ($quarters as $qName => $months)
            @foreach ($months as $monthName)
                <td style="text-align: right; padding-right: 5px;">{{ number_format($tableMonthTotals_SGD[++$iMonthCounter], 2) }}</td>
            @endforeach
            <td style="text-align: right; padding-right: 5px;">{{ number_format($tableQuarterTotals_SGD[$qName], 2) }}</td>
        @endforeach
        
        <td style="text-align: right; padding-right: 5px;">{{ number_format($tableGrandTotal_SGD = array_sum($tableMonthTotals_SGD), 2) }}</td>
    </tr>
    
    {{-- Reset month counter for MYR row --}}
    @php $iMonthCounter = 0; @endphp 
    
    <tr style="background-color: #dcdcdc; font-weight: bold;">
        <td  >MYR</td> 
        {{-- The first two columns are merged, only need the remaining cells --}}
        @foreach ($quarters as $qName => $months)
            @foreach ($months as $monthName)
                <td style="text-align: right; padding-right: 5px;">{{ number_format($tableMonthTotals_MYR[++$iMonthCounter], 2) }}</td>
            @endforeach
            <td style="text-align: right; padding-right: 5px;">{{ number_format($tableQuarterTotals_MYR[$qName], 2) }}</td>
        @endforeach
        
        <td style="text-align: right; padding-right: 5px;">{{ number_format($tableGrandTotal_MYR = array_sum($tableMonthTotals_MYR), 2) }}</td>
    </tr>
</tbody>
    </table>
    
    <button type="submit" class="btn btn-primary mt-3">Save Forecast</button>
</form>
</div>

<!-- This modal should be defined once outside your main data loop -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-labelledby="addCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-xl shadow-2xl">
            <div class="modal-header bg-indigo-500 text-white rounded-t-xl">
                <h5 class="modal-title font-bold text-lg" id="addCompanyModalLabel">
                    Add Company for: <span id="modal_individual_name" class="font-normal"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="addCompanyForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="sf_individual_id" id="modal_sf_individual_id" value="">
                    <!-- This hidden input will be populated by JavaScript -->
                    <input type="hidden" name="individually_id" id="modal_individually_id" value="">
                    
                    <div class="mb-3">
                        <label for="company_id" class="form-label font-medium">Company Name:</label>
                        <!-- Assuming you have a list of companies to iterate over -->
                        <input type="text" class="form-control rounded-md" id="company" name="company" required>
                    </div>

                    <div class="mb-3">
                         <label for="company_currency" class="form-label font-medium">Currency:</label>
                        <select class="form-control rounded-md" id="sales_forecast_currency" name="sales_forecast_currency" required>
                            <option value="">Select Currency</option>
                            <option value="SGD">SGD</option>
                            <option value="MYR">MYR</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 rounded-b-xl">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary bg-indigo-600 hover:bg-indigo-700 text-white" id="saveCompanyBtn">Save Company</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const addCompanyModalElement = document.getElementById('addCompanyModal');
    
    // 1. Context Transfer on Modal Show
    addCompanyModalElement.addEventListener('show.bs.modal', function (event) {
        // Button that triggered the modal
        const button = event.relatedTarget;                            
        
        // Extract info from data-bs-* attributes
        const individualId = button.getAttribute('data-individual-id');
        const SfIndividualId = button.getAttribute('data-sf-individual-id');
        const individualName = button.getAttribute('data-individual-name');

        // Update the modal's content
        const modalSfIndividualIdInput = addCompanyModalElement.querySelector('#modal_sf_individual_id');
        const modalIndividualIdInput = addCompanyModalElement.querySelector('#modal_individually_id');
        const modalIndividualNameSpan = addCompanyModalElement.querySelector('#modal_individual_name');

        if (modalSfIndividualIdInput) {
            modalSfIndividualIdInput.value = SfIndividualId;
        }
        
        // Set the hidden input value for API submission
        if (modalIndividualIdInput) {
            modalIndividualIdInput.value = individualId;
        }

        if (modalIndividualNameSpan) {
            modalIndividualNameSpan.textContent = individualName || 'N/A';
        }
    });


    // 2. API Submission Handler for the Modal Form
    const addCompanyForm = document.getElementById('addCompanyForm');
    const saveCompanyButton = document.getElementById('saveCompanyBtn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const addCompanyModal = new bootstrap.Modal(addCompanyModalElement);

    if (addCompanyForm && saveCompanyButton) {
        addCompanyForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Stop default form submission

            // Collect data from the form
            const formData = new FormData(addCompanyForm);
            
            const data = {};
            formData.forEach((value, key) => (data[key] = value));

            // Basic client-side check
            if (!data.company || !data.sales_forecast_currency) {
                // IMPORTANT: Use a custom UI element instead of alert() in production
                alert('Please enter a company and ensure the currency is present.');
                return;
            }
            
            // Disable button while processing
            saveCompanyButton.disabled = true;
            saveCompanyButton.textContent = 'Saving...';
            
            // Perform the Fetch API call
            fetch('{{ route("v1.sales-forecast.add-company") }}', { // *** ADJUST ROUTE NAME ***
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken 
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                console.log('Company added successfully:', data);
                
                // Close Modal and Update UI
                addCompanyModal.hide();
                
                // Reload or update the specific table area to show the new company row
                window.location.reload(); 
            })
            .catch(error => {
                console.error('Error adding company:', error);
                
                let message = 'An unknown error occurred.';
                if (error.message && typeof error.message === 'string') {
                    message = error.message; 
                } else if (error.errors) {
                    // Handle Laravel validation errors (422 status)
                    message = Object.values(error.errors).flat().join('\n');
                }
                
                // IMPORTANT: Use a custom UI element instead of alert()
                alert('Failed to add company: ' + message);
            })
            .finally(() => {
                saveCompanyButton.disabled = false;
                saveCompanyButton.textContent = 'Save Company';
            });
        });
    }
});
</script>

<div class="modal fade" id="addVariableModal" tabindex="-1" aria-labelledby="addVariableModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addVariableModalLabel">Add New Variable</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            
                            {{-- Form for adding the new variable --}}
                            <form method="POST" action="{{ route('v1.sales-forecast.add-variable') }}">
                                @csrf
                                <div class="modal-body">                                    
                                    <div class="mb-3">
                                        <input type="hidden" name="sales_forecasts_id" id="sales_forecasts_id" value="{{ $forecastId }}">
                                        <label for="variable_name" class="form-label">Variable Name:</label>
                                        <select type="text" class="form-control" id="individually_id" name="individually_id" required>
                                            <option value="">Choose Variable</option>
                                            @foreach(\App\Models\Individual::all() as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" id="saveVariableBtn">Save Variable</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('addVariableForm');
    const saveButton = document.getElementById('saveVariableBtn');
    
    // Get the CSRF token from the meta tag (ensure you have this in your <head>)
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Get the Bootstrap modal instance if using Bootstrap 5
    const addVariableModal = new bootstrap.Modal(document.getElementById('addVariableModal'));

    saveButton.addEventListener('click', function (e) {
        e.preventDefault(); // Stop the default form submission

        // Collect data
        const forecastId = document.getElementById('sales_forecasts_id').value;
        const individualId = document.getElementById('individually_id').value;
        
        if (!individualId) {
            alert('Please choose a variable.');
            return;
        }

        // 1. Prepare the API payload
        const data = {
            sales_forecasts_id: forecastId,
            individually_id: individualId
        };
        
        // Disable button while processing
        saveButton.disabled = true;
        saveButton.textContent = 'Saving...';

        // 2. Perform the Fetch API call
        fetch('{{ route("v1.sales-forecast.add-variable") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken // Laravel CSRF protection
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            // Check for non-2xx status codes (e.g., 422 validation errors)
            if (!response.ok) {
                // If the response is JSON (like validation errors), parse and throw
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            // Success response (from the updated controller below)
            console.log('Success:', data);
            
            // 3. Close Modal and Update UI
            addVariableModal.hide();
            
            // OPTION A: Reload the whole page to show the new variable (simplest)
            window.location.reload(); 
            
            // OPTION B (Advanced): If you want to avoid a full reload,
            // you would use the 'data' returned from the server to
            // dynamically append a new row to your HTML table here.

        })
        .catch(error => {
            console.error('Error:', error);
            
            let message = 'An unknown error occurred.';
            if (error.message && error.message.includes("is already linked")) {
                 message = error.message; // Use the specific error message from the controller
            } else if (error.errors) {
                // Handle Laravel validation errors (422 status)
                message = Object.values(error.errors).flat().join('\n');
            }
            
            alert('Failed to add variable: ' + message);
        })
        .finally(() => {
            saveButton.disabled = false;
            saveButton.textContent = 'Save Variable';
        });
    });
});
</script>


<script>
    // Placeholder for JavaScript function to perform front-end calculation on input change
    function calculateRow(inputElement) {
        // Implement the logic to recalculate the Q-Total and Grand Total based on user input
        // Example:
        // let row = inputElement.closest('tr');
        // let rowInputs = row.querySelectorAll('input[type="number"]');
        // let grandTotal = 0;
        // rowInputs.forEach(input => grandTotal += parseFloat(input.value) || 0);
        // row.querySelector('[data-grand-total]').textContent = grandTotal.toFixed(2);
    }
</script>

            </div>
        </div>
    </div>
</div>


   
@endsection
