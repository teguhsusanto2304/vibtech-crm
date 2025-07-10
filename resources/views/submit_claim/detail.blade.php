@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Breadcrumb -->
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

            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('errors'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('errors') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        <div id="msg"></div>
        <!-- DataTable Dependencies -->


        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
        <!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

<!-- FixedHeader CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/4.0.1/css/fixedHeader.dataTables.min.css">
<!-- FixedHeader JS -->
<script type="text/javascript" src="https://cdn.datatables.net/fixedheader/4.0.1/js/dataTables.fixedHeader.min.js"></script>

<!-- NEW: RowGroup CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.5.0/css/rowGroup.dataTables.min.css">
<!-- NEW: RowGroup JS -->
<script type="text/javascript" src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.min.js"></script>
<style>
    .dt-amount-right {
    text-align: right !important; /* !important to override DataTables' default alignment */
}
</style>
        <!-- Card -->
        <div class="card">
    <div class="card-header bg-default text-white p-4 rounded-t-lg">
                <div class="flex justify-between items-center text-end">
                    <!-- Fixed: Added content to the h5 tag -->
                    <h5 class="mb-0 text-xl font-semibold"></h5>
                    <div class="flex space-x-2">
                        @if(request()->query('from'))
                         <a href="{{ route('v1.submit-claim.all') }}" class="btn btn-light bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md text-sm flex items-center shadow-sm btn-sm">
                            <i class="fas fa-arrow-left me-1 mr-2"></i> Back to Claims List
                        </a>
                        @else
                        <a href="{{ route('v1.submit-claim.list') }}" class="btn btn-light bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md text-sm flex items-center shadow-sm btn-sm">
                            <i class="fas fa-arrow-left me-1 mr-2"></i> Back to Claims List
                        </a>
                        @endif
                        @if($claim->data_status==1 || $claim->data_status==4)
                        <a href="#" class="btn btn-info bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm flex items-center shadow-sm btn-sm submit-claim-status-btn"
                            data-id="{{ $claim->obfuscated_id }}"       {{-- Pass the obfuscated ID of the main SubmitClaim --}}
                            data-new-status="2">                          {{-- The new status value (e.g., 1 for 'submitted') --}}
                                Submit This Claim
                        </a>
                        <a href="{{ route('v1.submit-claim.create') }}?id={{ $claim->obfuscated_id }}" class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm flex items-center shadow-sm btn-sm">
                            Create a new Claim Item
                        </a>
                        @endif
                        @can('view-all-submit-claim')
                            @if($claim->data_status==2 && request()->query('from'))
                        <a href="#" class="btn btn-success bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm flex items-center shadow-sm btn-sm submit-claim-status-btn"
                            data-id="{{ $claim->obfuscated_id }}"       {{-- Pass the obfuscated ID of the main SubmitClaim --}}
                            data-new-status="3">                          {{-- The new status value (e.g., 1 for 'submitted') --}}
                                Approve This Claim
                        </a>
                        <a href="#" class="btn btn-danger bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm flex items-center shadow-sm btn-sm submit-claim-status-btn"
                            data-id="{{ $claim->obfuscated_id }}"       {{-- Pass the obfuscated ID of the main SubmitClaim --}}
                            data-new-status="4">                          {{-- The new status value (e.g., 1 for 'submitted') --}}
                                Reject This Claim
                        </a>
                        @endif
                        @endcan
                        
                    </div>
                </div>
            </div>

    <div class="card-body" >
        <div class="row gy-4">
            <!-- Left Column -->
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Serial Number:</strong>
                    <div>{{ $claim->serial_number }}</div>
                </div>
                <div class="mb-3">
                    <strong>Claim Date:</strong>
                    <div>{{ $claim->claim_date->format('d M Y') }}</div>
                </div>
                <div class="mb-3">
                    <strong>Staff Name:</strong>
                    <div>{{ $claim->staff->name ?? 'N/A' }}</div>
                </div>
                <div class="mb-3">
                    <strong>Description:</strong>
                    <div>{{ $claim->description ?? 'N/A' }}</div>
                </div>
                {{-- Uncomment if needed
                <div class="mb-3">
                    <strong>Claim Type:</strong>
                    <div>{{ $claim->claimType->name ?? 'N/A' }}</div>
                </div>
                --}}
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
                
                <div class="mb-3">
                    <strong>Status:</strong>
                    <div>
                        {!! $claim->submit_claim_status !!}
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Created At:</strong>
                    <div>{{ $claim->created_at->format('d M Y H:i:s') }}</div>
                </div>
                <div class="mb-3 text-end">
                    <strong>Total Amount:</strong>
                    <div><ul class="list-group mb-4">
        @forelse($claim->total_by_currency as $totalByCurrency)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>{{ $totalByCurrency['currency'] }} Total :</strong>
                {{ $totalByCurrency['formatted_total'] }}
            </li>
        @empty
            <li class="list-group-item">No claim items with amounts found.</li>
        @endforelse
    </ul>
</div>
                </div>
            </div>
        </div>
        <div >
            <input type="hidden" id="submitClaimId" value="{{ $claim->id }}"> 
            <div class="table-responsive">
            <table class="table table-bordered table-striped nowrap w-100" id="submit-claim-item-table">
                <thead>
                    <tr>
                        <th>No</th> <!-- For addIndexColumn() -->
                        <th>Claim Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Created Date</th>
                        <th class="dt-amount-right">Amount</th>
                        <th class="d-none">Currency</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                                   <!-- DataTables will populate this tbody via AJAX -->
                </tbody>
                <tfoot>
        <!-- NEW: Add a tfoot for summary rows -->
        <tr>
            <th colspan="5" style="text-align:right">Total:</th>
            <th class="dt-amount-right"></th> <!-- This will be for the sum of amounts -->
            <th></th> <!-- Empty for hidden currency column -->
            <th></th> <!-- Empty for action column -->
        </tr>
    </tfoot>
            </table>
            </div>

            <!-- Submit Claim Item Details Modal -->
            <div class="modal fade" id="submitClaimItemDetailModal" tabindex="-1" aria-labelledby="submitClaimItemDetailModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="submitClaimItemDetailModalLabel">Submit Claim Item Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Claim Type:</strong> <span id="modalClaimType"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Amount:</strong> <span id="modalAmountCurrency"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Start Date:</strong> <span id="modalStartDate"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>End Date:</strong> <span id="modalEndDate"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Created Date:</strong> <span id="modalCreatedDate"></span>
                                </div>
                                
                            </div>
                            

                            <hr>

                            <h5>Associated Files</h5>
                            <div id="modalFilesList" class="list-group">
                                <!-- Files will be loaded here by JavaScript -->
                                <p class="text-muted" id="noFilesMessage">No files attached.</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
        $(document).ready(function() {
            

            $('.submit-claim-status-btn').on('click', function(e) {
                e.preventDefault(); // Prevent default link behavior

                const claimId = $(this).data('id');
                const newStatus = $(this).data('new-status');
                const buttonText = $(this).text().trim(); // Get the current button text for confirmation

                if (!claimId || newStatus === undefined) {
                    console.error("Claim ID or new status is missing for status update.");
                    alert("Error: Missing claim ID or status information.");
                    return;
                }

                // Confirmation dialog
                if (confirm(`Are you sure you want to submit this claim?`)) {
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    const updateUrl = `/v1/submit-claim/${claimId}/update-status`; // Use the new API route

                    $.ajax({
                        url: updateUrl,
                        type: 'POST', // Or 'PUT'/'PATCH' if you prefer, but POST is simpler for status updates
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: {
                            new_status: newStatus // Send the new status value
                        },
                        success: function(response) {
                            alert(response.message);
                            // After successful update, you might want to:
                            // 1. Reload the DataTable (if it displays the main claims list)
                            //    If this button is on a page showing *items* for a claim,
                            //    and the main claim's status is displayed elsewhere, you might need a full page refresh.
                            //    If it's on a claims *list* page, reload that DataTable.
                            //    Example: If you have a main claims DataTable with ID 'mainClaimsTable'
                            //    $('#mainClaimsTable').DataTable().ajax.reload(null, false);

                            // 2. Or, if this button is on a detail page and you want to refresh everything:
                            location.reload(); // Full page refresh
                        },
                        error: function(xhr, status, error) {
                            console.error("Error updating claim status:", status, error, xhr.responseText);
                            let errorMessage = 'Failed to update claim status.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                try {
                                    const errorObj = JSON.parse(xhr.responseText);
                                    if (errorObj.message) errorMessage = errorObj.message;
                                } catch (e) { /* ignore */ }
                            }
                            alert(errorMessage);
                        }
                    });
                }
            });

            $('#submit-claim-item-table').on('click', '.delete-item-btn', function(e) {
                e.preventDefault(); // Prevent the default link behavior

                const itemId = $(this).data('id'); // Get the item ID from the data-id attribute
                const deleteUrl = `/v1/submit-claim/submit-claim-items/${itemId}/destroy`; // Construct the delete URL

                // Confirmation dialog
                if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    // Get CSRF token from meta tag (Laravel's default)
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE', // Use DELETE HTTP method
                        headers: {
                            'X-CSRF-TOKEN': csrfToken // Send CSRF token for Laravel protection
                        },
                        success: function(response) {
                            alert(response.message); // Show success message
                            location.reload(); 
                            //$('#submit-claim-item-table').DataTable().ajax.reload(null, false); // Reload the DataTable without resetting pagination
                        },
                        error: function(xhr, status, error) {
                            console.error("Error deleting item:", status, error, xhr.responseText);
                            let errorMessage = 'Failed to delete item.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            alert(errorMessage); // Show error message
                        }
                    });
                }
            });

            // Event listener for the "View" button in the DataTables action column
            $('#submit-claim-item-table').on('click', '.view-item-btn', function(e) {
                e.preventDefault(); // Prevent default link behavior
                const itemId = $(this).data('id'); // Get the item ID from data-id attribute

                if (!itemId) {
                    console.error("Item ID not found for view button.");
                    return;
                }

                // Show a loading indicator (optional)
                $('#modalClaimType').text('Loading...');
                $('#modalAmountCurrency').text('Loading...');
                $('#modalStartDate').text('Loading...');
                $('#modalEndDate').text('Loading...');
                $('#modalCreatedDate').text('Loading...');
                $('#modalFilesList').html('<p class="text-muted">Loading files...</p>');
                $('#noFilesMessage').hide(); // Hide no files message while loading

                // Make AJAX call to fetch item details
                $.ajax({
                    url: `/v1/submit-claim/submit-claim-items/${itemId}/details`, // Use the new API route
                    method: 'GET',
                    success: function(response) {
                        // Populate modal with data
                        $('#modalClaimType').text(response.claim_type_name);
                        $('#modalAmountCurrency').text(response.amount_currency);
                        $('#modalStartDate').text(response.start_date);
                        $('#modalEndDate').text(response.end_date);
                        $('#modalCreatedDate').text(response.created_at_formatted);
                        // Populate files list
                        const filesListDiv = $('#modalFilesList');
                        filesListDiv.empty(); // Clear previous files

                        if (response.files && response.files.length > 0) {
                            response.files.forEach(function(file) {
                                let fileIcon = '';
                                // Basic icon based on mime type
                                if (file.mime_type.includes('image')) {
                                    fileIcon = '<i class="fas fa-image me-2"></i>';
                                } else if (file.mime_type.includes('pdf')) {
                                    fileIcon = '<i class="fas fa-file-pdf me-2"></i>';
                                } else if (file.mime_type.includes('word')) {
                                    fileIcon = '<i class="fas fa-file-word me-2"></i>';
                                } else if (file.mime_type.includes('excel')) {
                                    fileIcon = '<i class="fas fa-file-excel me-2"></i>';
                                } else {
                                    fileIcon = '<i class="fas fa-file me-2"></i>';
                                }
                                filesListDiv.append(`
                                    <a href="${file.url}" class="list-group-item list-group-item-action d-flex align-items-center" target="_blank">
                                        ${fileIcon} ${file.name}
                                    </a>
                                `);
                            });
                            $('#noFilesMessage').hide();
                        } else {
                            filesListDiv.append('<p class="text-muted">No files attached.</p>');
                        }

                        // Show the modal
                        const detailModal = new bootstrap.Modal(document.getElementById('submitClaimItemDetailModal'));
                        detailModal.show();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching item details:", status, error, xhr.responseText);
                        alert("Failed to load item details. Please try again.");
                        // Reset modal content or show error message
                        $('#modalClaimType').text('Error');
                        $('#modalDescription').html('Failed to load data.');
                        $('#modalFilesList').html('<p class="text-danger">Error loading files.</p>');
                    }
                });
            });
            // Get the submit_claim_id from a hidden input or JavaScript variable
            const submitClaimId = $('#submitClaimId').val(); 
            // Or if you pass it directly in JS: const submitClaimId = 123;

            if (!submitClaimId) {
                console.error("submit_claim_id is not defined. Cannot initialize DataTable.");
                
                return;
            }

            const dataTable = $('#submit-claim-item-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: `/v1/submit-claim/${submitClaimId}/item-list/data`,
                    type: 'GET'
                },
                // REMOVED: 'claim_status' column
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // 0
                    { data: 'claim_type', name: 'claim_type' },                                    // 1
                    { data: 'start_date', name: 'start_date' },                                    // 2
                    { data: 'end_date', name: 'end_date' },                                        // 3
                    { data: 'created_date', name: 'created_date' },                                // 4
                    // REMOVED: { data: 'claim_status', name: 'claim_status', orderable: false, searchable: false },
                    { data: 'amount_currency', name: 'amount_currency' },                          // 5 - ADJUSTED INDEX
                    { data: 'currency', name: 'currency', visible: false },                        // 6 - ADJUSTED INDEX
                    { data: 'action', name: 'action', orderable: false, searchable: false },       // 7 - ADJUSTED INDEX
                ],
                columnDefs: [
                    { 
                        targets: 5, // ADJUSTED: Target 'amount_currency' is now at index 5
                        className: 'dt-body-right dt-head-right' 
                    }
                ],
                // ADJUSTED: Order by 'currency' column (now at index 6)
                order: [[1, 'asc']], 
                rowGroup: {
                    dataSrc: 'currency',
                    startRender: function (rows, group) {
                        let sumAmount = rows.data().reduce(function (a, b) {
                            const amountStr = String(b.amount_currency).split(' ')[1]; 
                            return a + parseFloat(amountStr.replace(/,/g, '')) || 0; 
                        }, 0);

                        const formattedSum = new Intl.NumberFormat('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(sumAmount);

                        return $('<tr/>')
                            // ADJUSTED: colspan="5" because there are 5 visible columns before 'Amount' (0 to 4)
                            .append('<td colspan="5" class="group-header"><strong>Currency: ' + group + '</strong></td>')
                            .append('<td class="group-total text-end"><strong>' + group + ' ' + formattedSum + '</strong></td>')
                            // ADJUSTED: colspan="2" for the remaining two columns (hidden currency, action)
                            .append('<td colspan="2"></td>'); 
                    }
                },
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();

                    // ADJUSTED: Target 'amount_currency' column (now at index 5)
                    let totalAmount = api.column(5, { page: 'current' }).data().reduce(function (a, b) {
                        const amountStr = String(b).split(' ')[1]; 
                        return a + parseFloat(amountStr.replace(/,/g, '')) || 0;
                    }, 0);

                    // Update footer
                    // ADJUSTED: Target the correct footer cell (index 5 for Amount)
                    $(api.column(5).footer()).html(
                        '<strong>' + new Intl.NumberFormat('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(totalAmount) + '</strong>'
                    );
                }
            });
            $(window).on('load', function() {
                dataTable.columns.adjust().draw();
            });
        });
        
    </script>
        </div>
    </div>



        
        

    </div>

            </div>
        </div>

                            
@endsection
