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

        <!-- Card -->
        <div class="card">
    <div class="card-header bg-default text-white p-4 rounded-t-lg">
                <div class="flex justify-between items-center text-end">
                    <!-- Fixed: Added content to the h5 tag -->
                    <h5 class="mb-0 text-xl font-semibold"></h5>
                    <div class="flex space-x-2">
                        @if(request()->query('from'))
                         <a href="{{ route('v1.submit-claim.list') }}" class="btn btn-light bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md text-sm flex items-center shadow-sm btn-sm">
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
                    <div><h4>{{ $claim->currency }} {{ number_format($claim->total_amount, 2) }}</h4></div>
                </div>
            </div>
        </div>
        <div >
            <input type="hidden" id="submitClaimId" value="{{ $claim->id }}"> 
            <table class="table table-bordered table-striped nowrap w-100" id="submit-claim-item-table">
                <thead>
                    <tr>
                        <th>No</th> <!-- For addIndexColumn() -->
                        <th>Claim Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Created Date</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                                   <!-- DataTables will populate this tbody via AJAX -->
                </tbody>
            </table>

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

            $('#submit-claim-item-table').DataTable({
                processing: true,      // Show processing indicator
                serverSide: true,      // Enable server-side processing
                scrollY: '400px', // Set a fixed height for vertical scrolling
                scrollCollapse: true, // Allows the table to shrink if data is less than scrollY height
                scrollX: true, // Enable horizontal scrolling if content overflows
                fixedHeader: true, 
                ajax: {
                    // Construct the AJAX URL using the route helper (if in Blade)
                    // Or hardcode it if you're not using Blade's route() helper
                    url: `/v1/submit-claim/${submitClaimId}/item-list/data`, // Example: /submit-claim-items-data/123
                    type: 'GET'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // For addIndexColumn()
                    { data: 'claim_type', name: 'claim_type' },
                    { data: 'start_date', name: 'start_date' },
                    { data: 'end_date', name: 'end_date' },
                    { data: 'created_date', name: 'created_date' },
                    { data: 'amount_currency', name: 'amount_currency' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }, // Contains HTML, not directly sortable/searchable by DB
                ]
            });
        });
    </script>
        </div>
    </div>



        
        

    </div>

            </div>
        </div>

                            
@endsection
