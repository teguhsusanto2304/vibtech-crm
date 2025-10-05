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
    /* Minimal CSS for a spreadsheet look */
    table { 
        border-collapse: collapse; 
        font-family: Arial, sans-serif; 
        
        /* 💡 ADD THIS LINE: Forces the browser to honor explicit column widths */
    }
    th, td { border: 1px solid #ccc; padding: 4px; text-align: center; }
    thead th { background-color: #c2c1beff; font-weight: bold; }
    .variable-col { text-align: left; background-color: #f8f9fa; }
    .input-field { width: 80px; text-align: right; border: none; } /* Slightly wider input */
    .table-responsive-scroll {
            overflow-x: auto; /* Enable horizontal scrollbar when content is too wide */
                  /* Ensure the container takes up available width */
        }
</style>
<div class="table-responsive-scroll"> 
<form method="GET" action="{{ route('v1.sales-forecast.list') }}">
    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="projectStartDate" class="form-label">Forecast Year</label>
                            <input type="number" name="year" id="year" class="form-control" min="{{ date('Y')-5 }}" max="{{ date('Y')+5 }}" value="{{ $year }}">
                        </div>
                        <div class="col-md-2">
                            <label for="projectStartDate" class="form-label invisible">Create Forecast Year</label>
                            <button type="submit" class="btn btn-info">Search</a>
                        </div>
                        <div class="col-md-3">
                            <label for="projectStartDate" class="form-label invisible">Create Forecast Year</label>
                            <a href="{{ route('v1.sales-forecast.create') }}" class="btn btn-secondary">Add Variable</a>
                        </div>
    </div>
</form>
<form method="POST" action="{{ route('v1.sales-forecast.save') }}">
    @csrf
    <table>
        <thead>
            <tr>
                <th rowspan="3" class="variable-col" style="width: 300px;">Variable</th>
                <th rowspan="3" class="variable-col" style="width: 250px;">Company</th>
                <th rowspan="3" class="variable-col" style="width: 250px;">Viewer</th> 
                @foreach ($quarters as $qName => $months)
                    {{-- Colspan is now the number of months + 1 for the Quarter Total --}}
                    <th colspan="{{ count($months) + 1 }}">{{ $qName }}</th>
                @endforeach
                <th rowspan="3">Grand Total</th>
            </tr>
            
            <tr>
                @foreach ($quarters as $qName => $months)
                    @foreach ($months as $monthName)
                        <th>{{ $monthName }}</th> {{-- Each month is now a single column --}}
                    @endforeach
                    <th>{{ $qName }} Total</th>
                @endforeach
            </tr>
            
            <tr>
                @foreach ($quarters as $qName => $months)
                    @foreach ($months as $monthName)
                        <th></th> {{-- Only one column header per month --}}
                    @endforeach
                    <th></th>
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
                $iMonth = 0;
                // Get the currency for the current row

                $currency = $individual->pivot->currency ?? 'N/A';
                
            @endphp
            
            <tr>
                {{-- ... (Your Variable Name Column remains the same, rowspan will need to be +2 or more now) ... --}}
                @if ($firstRow)
                    {{-- Change rowspan to $rowspanCount + 2 if you add the currency summary row for the group --}}
                    <td class="variable-col" rowspan="{{ $rowspanCount  }}" style="font-weight: bold;">
                        {{ $individualName }} 
                    </td>
                    @php $firstRow = false; @endphp
                @endif
                
                {{-- 2. COMPANY NAME COLUMN --}}
                <td class="variable-col">
                    {{ $individual->pivot->company ?? 'N/A' }} 
                    <p><small><span class="badge {{ $currency=='SGD' ? 'bg-info':'bg-warning' }} ">{{ $currency }}</span></small></p>
                </td>
                
                {{-- 3. MONTHLY FORECAST COLUMNS (Accumulation Logic) --}}
                @foreach ($quarters as $qName => $months)
                    @php $quarterTotal = 0; @endphp
                    @foreach ($months as $monthIndex => $monthName)
                        @php
                            $iMonth++; 
                            $monthDate = date('Y_m', strtotime($monthName));
                            $key = $individual->pivot->id.'_'.$iMonth;
                            $savedValue = $forecastValues->get($key)->amount ?? 0;
                            
                            $quarterTotal += $savedValue;
                            $individualRowTotal += $savedValue;
                            
                            // 💡 ACCUMULATION SPLIT BY CURRENCY
                            if ($individual->pivot->currency == 'SGD') {
                                $variableMonthTotals_SGD[$iMonth - 1] += $savedValue; // Variable Group SGD Total
                                $tableMonthTotals_SGD[$iMonth] += $savedValue;       // Table Grand SGD Total
                            } elseif ($individual->pivot->currency == 'MYR') {
                                $variableMonthTotals_MYR[$iMonth - 1] += $savedValue; // Variable Group MYR Total
                                $tableMonthTotals_MYR[$iMonth] += $savedValue;       // Table Grand MYR Total
                            }
                        @endphp
                        {{-- ... (Your Input Field HTML remains the same) ... --}}
                        <td> 
                            <input 
                                type="number" 
                                class="input-field" 
                                name="forecast[{{ $individual->pivot->id }}][{{ $individual->pivot->id.'_'.$individual->pivot->individually_id.'_'.$monthDate }}][amount]" 
                                value="{{ number_format($savedValue, 2, '.', '') }}" 
                                step="0.01" 
                            >
                        </td>
                    @endforeach
                    
                    {{-- Q Total for the current Company Row --}}
                    <td data-quarter-total="{{ $qName }}"><strong style="color: #f51313ff;">{{ number_format($quarterTotal, 2) }}</strong></td>
                    
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
                <td data-grand-total><strong style="color: #480ee8ff;">{{ number_format($individualRowTotal, 2) }}</strong></td>
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
                        <td>{{ number_format($variableMonthTotals_SGD[$iMonthCounter++], 2) }}</td>
                    @endforeach
                    <td>{{ number_format($variableQuarterTotals_SGD[$qName], 2) }}</td>
                @endforeach
                <td>{{ number_format($variableGrandTotal_SGD, 2) }}</td>
            </tr>
            
            {{-- Reset month counter for MYR row --}}
            @php $iMonthCounter = 0; @endphp 
            
            <tr style="background-color: #e6e6fa; font-weight: bold;">
                {{-- Variable Name column is merged above, only need Company column --}}
                <td>MYR</td>
                @foreach ($quarters as $qName => $months)
                    @foreach ($months as $monthName)
                        <td>{{ number_format($variableMonthTotals_MYR[$iMonthCounter++], 2) }}</td>
                    @endforeach
                    <td>{{ number_format($variableQuarterTotals_MYR[$qName], 2) }}</td>
                @endforeach
                <td>{{ number_format($variableGrandTotal_MYR, 2) }}</td>
            </tr>
        @endif
    @endforeach

    {{-----------------------------------------------------------}}
    {{-- FINAL TABLE GRAND SUMMARY ROW (SPLIT BY CURRENCY) --}}
    {{-----------------------------------------------------------}}
    @php
        $iMonthCounter = 0; // Reset counter
    @endphp
    
    <tr style="background-color: #dcdcdc; font-weight: bold; border-top: 3px double #333;">
        <td colspan="2" rowspan="2">GRAND TOTAL</td>
        
        {{-- SGD Row --}}
        @foreach ($quarters as $qName => $months)
            @foreach ($months as $monthName)
                <td>{{ number_format($tableMonthTotals_SGD[++$iMonthCounter], 2) }}</td>
            @endforeach
            <td>{{ number_format($tableQuarterTotals_SGD[$qName], 2) }}</td>
        @endforeach
        
        <td>{{ number_format($tableGrandTotal_SGD = array_sum($tableMonthTotals_SGD), 2) }}</td>
    </tr>
    
    {{-- Reset month counter for MYR row --}}
    @php $iMonthCounter = 0; @endphp 
    
    <tr style="background-color: #dcdcdc; font-weight: bold;">
        {{-- The first two columns are merged, only need the remaining cells --}}
        @foreach ($quarters as $qName => $months)
            @foreach ($months as $monthName)
                <td>{{ number_format($tableMonthTotals_MYR[++$iMonthCounter], 2) }}</td>
            @endforeach
            <td>{{ number_format($tableQuarterTotals_MYR[$qName], 2) }}</td>
        @endforeach
        
        <td>{{ number_format($tableGrandTotal_MYR = array_sum($tableMonthTotals_MYR), 2) }}</td>
    </tr>
</tbody>
    </table>
    
    <button type="submit" class="btn btn-primary mt-3">Save Forecast</button>
</form>
</div>

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
