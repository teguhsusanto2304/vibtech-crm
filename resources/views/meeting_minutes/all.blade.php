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
            <div class="card-header text-white p-4 rounded-t-lg d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-xl font-semibold">Meeting Minutes Records</h5>
                <div class="d-flex">
                    <a href="{{ route('v1.meeting-minutes.create') }}" class="btn btn-success me-2">
                        <i class="fas fa-plus"></i> Record New Minutes
                    </a>
                    <button type="button" class="btn btn-warning" id="bulkExportBtn">
                        <i class="fas fa-file-archive"></i> Bulk Export PDFs
                    </button>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-3 mb-2">
                        <label for="filter_month" class="form-label">Filter by Month:</label>
                        <select class="form-select" id="filter_month">
                            <option value="">All Months</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ (int)date('m') === $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $i, 1)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
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
                    <div class="col-md-6 d-flex align-items-end mb-2">
                        <button type="button" class="btn btn-primary me-2" id="applyFilterBtn">
                            <i class="fas fa-filter"></i> Apply Filter
                        </button>
                        <button type="button" class="btn btn-secondary" id="resetFilterBtn">
                            <i class="fas fa-sync-alt"></i> Reset Filter
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped nowrap w-100" id="meeting-minutes-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Meeting Minutes Date</th>
                                <th>Meeting Minutes Time</th>
                                <th>Meeting Topic</th>
                                <th>Recorded By</th>
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
            let meetingMinutesTable; // Declare DataTable instance globally in this scope

            function initializeDataTable(month = '', year = '') {
                // Destroy existing DataTable instance if it exists
                if ($.fn.DataTable.isDataTable('#meeting-minutes-table')) {
                    meetingMinutesTable.destroy();
                }

                meetingMinutesTable = $('#meeting-minutes-table').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollX: true,
                    responsive: false,
                    ajax: {
                        url: '{{ route("v1.meeting-minutes.list.data") }}',
                        type: 'GET',
                        data: function (d) {
                            d.month = month; // Pass month filter
                            d.year = year;   // Pass year filter
                            d.all = 'yes';
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'meeting_date_formatted', name: 'meeting_date', searchable: true }, // Filterable by date
                        { data: 'meeting_time_range', name: 'meeting_time', orderable: false, searchable: false },
                        { data: 'meeting_topic', name: 'topic', searchable: true },
                        { data: 'saved_by_user', name: 'saved_by_user', orderable: false, searchable: true },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    order: [[1, 'desc'], [2, 'desc']], // Default order by Meeting Minutes Date then Time (newest to oldest)
                    columnDefs: [
                        {
                            targets: [0, 1, 2, 4, 5], // Center relevant columns
                            className: 'dt-body-center dt-head-center'
                        }
                    ]
                });
            }

            // Initialize DataTable with default filters (current month/year)
            const currentMonth = $('#filter_month').val();
            const currentYear = $('#filter_year').val();
            initializeDataTable(currentMonth, currentYear);

            // Apply Filter Button
            $('#applyFilterBtn').on('click', function() {
                const month = $('#filter_month').val();
                const year = $('#filter_year').val();
                initializeDataTable(month, year);
            });

            // Reset Filter Button
            $('#resetFilterBtn').on('click', function() {
                const currentMonth = new Date().getMonth() + 1; // JS months are 0-indexed
                const currentYear = new Date().getFullYear();

                $('#filter_month').val(currentMonth.toString());
                $('#filter_year').val(currentYear.toString());
                initializeDataTable(currentMonth.toString(), currentYear.toString());
            });

            // Handle View PDF Button Click
            $(document).on('click', '.view-pdf-btn', function() {
                const meetingId = $(this).data('id');
                const pdfUrl = `{{ url('v1/meeting-minutes/${meetingId}/pdf-preview') }}`; // New route for PDF preview
                $('#pdfFrame').attr('src', pdfUrl);
                const pdfPreviewModal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
                pdfPreviewModal.show();
            });

            // Handle Download PDF Button Click
            $(document).on('click', '.download-pdf-btn', function() {
                const meetingId = $(this).data('id');
                const downloadUrl = `{{ url('v1/meeting-minutes/${meetingId}/download-pdf') }}`; // New route for PDF download
                window.location.href = downloadUrl;
            });

            // Handle Bulk Export Button Click
            $('#bulkExportBtn').on('click', function() {
                const month = $('#filter_month').val();
                const year = $('#filter_year').val();
                const exportUrl = `{{ route("v1.meeting-minutes.bulk-export-pdf") }}?month=${month}&year=${year}`;
                window.location.href = exportUrl;
            });

            // Helper function to display messages
            function displayMessage(type, message) {
                const msgDiv = $('#msg');
                msgDiv.html(`
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
                $('html, body').animate({
                    scrollTop: msgDiv.offset().top - 100
                }, 500);
            }
        });
    </script>
    @endsection
