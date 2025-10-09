@extends('layouts.app')

@section('title', $title)

@section('content')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom-icon">
                    @foreach ($breadcrumb as $item)
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);">{{ $item }}</a>
                            <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                        </li>
                    @endforeach
                </ol>
            </nav>

            <h3>{{ $title }}</h3>

            <div id="msg" class="mb-3"></div> </div>

        <div class="card">
            
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-3 mb-2">
                        <label for="filter_year" class="form-label">Filter by Year:</label>
                        <select class="form-select" id="filter_year">
                            <option value="">All Years</option>
                            @php
                                $currentYear = date('Y');
                                for ($year = $currentYear + 1; $year >= 2020; $year--) { // Adjust range as needed
                                    echo "<option value='{$year}'" . ($year == $currentYear ? ' selected' : '') . ">{$year}</option>";
                                }
                            @endphp
                        </select>
                    </div>
                    
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped nowrap w-100" id="salesForecastTable">
                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>Created By</th>
                                <th>Variable(s)</th>                                
                                <th>Companies</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfPreviewModalLabel">Meeting Minute PDF Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfFrame" style="width:100%; height:80vh;" frameborder="0"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#salesForecastTable').DataTable({
                // Configuration for Server-Side Processing
                processing: true, // Show a 'processing' indicator
                serverSide: true, // Tell DataTables to fetch data from the server
                
                // The URL of your Laravel API endpoint
                ajax: {
                    url: '{{ route("v1.sales-forecast.data") }}', // Replace with your actual route name/URL
                    type: 'GET',
                    data: function(d) {
                        d.filter_year = $('#filter_year').val(); // Add the selected year to the request data
                    }
                },
                
                // Define the mapping between table columns and the JSON data fields
                columns: [
                    { data: 'year', name: 'year' },
                    { data: 'created_by_name', name: 'creator.name', orderable: false }, // Use the custom column name
                    { data: 'individuals_count', name: 'individuals_count', searchable: false },
                    { data: 'distinct_companies_count', name: 'distinct_companies_count', searchable: false},
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                
                // Optional: Language settings, page length, etc.
                language: {
                    search: "Search:",
                    paginate: {
                        next: "Next &raquo;",
                        previous: "&laquo; Previous"
                    }
                }
            });
           

            // Optional: Trigger filter change on select box change (removes need for separate button)
            $('#filter_year').on('change', function() {
                $('#salesForecastTable').DataTable().draw();
            });
        });
    </script>
    @endsection
