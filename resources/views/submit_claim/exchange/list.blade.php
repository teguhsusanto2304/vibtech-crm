@extends('layouts.app') {{-- Assuming you have a master layout --}}

@section('title', 'Exchange Rates')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Dashboard /</span> Exchange Rates
    </h4>

    <div class="card mb-4">
        <div class="card-header bg-default text-white">
            <h5 class="mb-0 text-xl font-semibold">Exchange Rates</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="filterDate" class="form-label">Select Date:</label>
                    <input type="date" class="form-control" id="filterDate" value="{{ $today }}">
                </div>
                <div class="col-md-8 d-flex align-items-end">
                    <button type="button" class="btn btn-primary me-2" id="applyFilterBtn">Apply Filter</button>
                    <button type="button" class="btn btn-outline-secondary" id="resetFilterBtn">Reset Filter</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div id="exchange-rates-loading" class="text-center py-5 d-none">
                <div class="spinner-border text-default" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading exchange rates...</p>
            </div>
            <div id="exchange-rates-error" class="alert alert-default d-none" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> Failed to load exchange rates.
            </div>
            <div id="exchange-rates-empty" class="alert alert-default d-none" role="alert">
                <i class="fas fa-info-circle me-2"></i> No exchange rates found for the selected date.
            </div>

            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-striped" id="exchangeRatesTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Currency</th>
                            <th>Rate</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Rates will be loaded here via JavaScript --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        const filterDateInput = $('#filterDate');
        const applyFilterBtn = $('#applyFilterBtn');
        const resetFilterBtn = $('#resetFilterBtn');
        const exchangeRatesTableBody = $('#exchangeRatesTable tbody');
        const loadingSpinner = $('#exchange-rates-loading');
        const errorAlert = $('#exchange-rates-error');
        const emptyAlert = $('#exchange-rates-empty');

        // Function to fetch and display exchange rates
        function fetchAndDisplayRates(date = null) {
            loadingSpinner.removeClass('d-none');
            errorAlert.addClass('d-none');
            emptyAlert.addClass('d-none');
            exchangeRatesTableBody.empty(); // Clear existing rows

            let apiUrl = "{{ route('v1.submit-claim.exchange-rates.data') }}";
            if (date) {
                apiUrl += `?date=${date}`;
            }

            $.ajax({
                url: apiUrl,
                method: 'GET',
                success: function(response) {
                    loadingSpinner.addClass('d-none');
                    if (response.success && response.rates.length > 0) {
                        response.rates.forEach(function(rate) {
                            exchangeRatesTableBody.append(`
                                <tr>
                                    <td>${rate.rate_date}</td>
                                    <td>${rate.currency}</td>
                                    <td>${rate.rate}</td>
                                    <td>${rate.created_at}</td>
                                </tr>
                            `);
                        });
                    } else {
                        emptyAlert.removeClass('d-none');
                    }
                },
                error: function(xhr) {
                    loadingSpinner.addClass('d-none');
                    errorAlert.removeClass('d-none').text(xhr.responseJSON?.message || 'Failed to load exchange rates. Please try again.');
                    console.error('Error fetching exchange rates:', xhr.responseText);
                }
            });
        }

        // Initial load of rates when the page loads
        // Use the initial date from the input, which is set by Laravel
        fetchAndDisplayRates(filterDateInput.val());

        // Event listener for Apply Filter button
        applyFilterBtn.on('click', function() {
            const selectedDate = filterDateInput.val();
            fetchAndDisplayRates(selectedDate);
        });

        // Event listener for Reset Filter button
        resetFilterBtn.on('click', function() {
            const today = new Date().toISOString().slice(0, 10); // Get today's date in YYYY-MM-DD
            filterDateInput.val(today); // Reset date input to today
            fetchAndDisplayRates(today); // Fetch rates for today
        });
    });
</script>
@endsection



