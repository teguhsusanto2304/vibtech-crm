@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                        @if($claim->data_status==\App\Models\SubmitClaim::STATUS_SUBMIT)
                        <a href="#" class="btn btn-warning bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm flex items-center shadow-sm btn-sm submit-claim-status-btn"
                            data-id="{{ $claim->obfuscated_id }}"       {{-- Pass the obfuscated ID of the main SubmitClaim --}}
                            data-new-status="1">                          {{-- The new status value (e.g., 1 for 'submitted') --}}
                                Return to Draft
                        </a>
                        @endif
                        @if($claim->data_status==\App\Models\SubmitClaim::STATUS_DRAFT || $claim->data_status==\App\Models\SubmitClaim::STATUS_REJECTED)
                        <a href="#" class="btn btn-info bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm flex items-center shadow-sm btn-sm submit-claim-status-btn"
                            data-id="{{ $claim->obfuscated_id }}"       {{-- Pass the obfuscated ID of the main SubmitClaim --}}
                            data-new-status="2">                          {{-- The new status value (e.g., 1 for 'submitted') --}}
                                Submit This Claim
                        </a>
                        <a href="{{ route('v1.submit-claim.create') }}?id={{ $claim->obfuscated_id }}" class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm flex items-center shadow-sm btn-sm">
                            Add New Claim Item
                        </a>
                        <a href="#" class="btn btn-danger bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm flex items-center shadow-sm btn-sm submit-claim-status-btn"
                            data-id="{{ $claim->obfuscated_id }}"       {{-- Pass the obfuscated ID of the main SubmitClaim --}}
                            data-new-status="0">                          {{-- The new status value (e.g., 1 for 'submitted') --}}
                                Delete Entire Claim
                        </a>
                        @endif
                        @can('view-all-submit-claim')
                            @if($claim->data_status==\App\Models\SubmitClaim::STATUS_SUBMIT && request()->query('from'))
                        <button class="btn btn-success bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm flex items-center shadow-sm btn-sm"
                            data-bs-toggle="modal" data-bs-target="#approveClaimModal" data-claim-id="{{ $claim->obfuscated_id }}">                          {{-- The new status value (e.g., 1 for 'submitted') --}}
                                Complete Claim Process
                        </button>
                       
                        @endif
                        @endcan
                        <button type="button" class="btn btn-info print-page-btn">
                            <i class="fas fa-print"></i> Print Page
                        </button>
                        
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
                    <strong>Claim Title:</strong>
                    <div style="display: inline-flex; align-items: center;">
                        {{-- Display the description content --}}
                        <span class="me-2">{{ $claim->description ?? 'N/A' }}</span>
                        
                        {{-- 💡 Add the pencil icon here 💡 --}}
                        {{-- Use a link or button if it triggers an action --}}
                        <a href="#" 
                            class="text-primary edit-description-btn" 
                            title="Edit Claim Title"
                            data-bs-toggle="modal" 
                            data-bs-target="#editDescriptionModal"
                            data-claim-id="{{ $claim->id }}" 
                            data-current-description="{{ $claim->description ?? '' }}">
                            <i class="fas fa-pencil-alt"></i> 
                        </a>
                        <div class="modal fade" id="editDescriptionModal" tabindex="-1" aria-labelledby="editDescriptionModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form id="edit-description-form" method="POST" action="{{ route('v1.submit-claim.update-description', ['id' => '__CLAIM_ID__']) }}">
                                    @csrf
                                    @method('PUT') <input type="hidden" name="claim_id" id="modal-claim-id">
                                    
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editDescriptionModalLabel">Edit Claim Title</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="claim-description-input" class="form-label">Title</label>
                                                <textarea class="form-control" id="claim-description-input" name="description" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const editModal = document.getElementById('editDescriptionModal');
                            
                            // Listen for when the modal is about to be shown
                            editModal.addEventListener('show.bs.modal', function (event) {
                                // Button that triggered the modal
                                const button = event.relatedTarget; 

                                // Extract info from data-* attributes
                                const claimId = button.getAttribute('data-claim-id');
                                const currentDescription = button.getAttribute('data-current-description');

                                // Update the modal's inputs and form action
                                const form = editModal.querySelector('#edit-description-form');
                                const claimIdInput = editModal.querySelector('#modal-claim-id');
                                const descriptionInput = editModal.querySelector('#claim-description-input');
                                
                                // 1. Set the form action URL (Important for Laravel PUT route)
                                // Note: You must define the 'claims.update.description' route
                                const routeTemplate = form.getAttribute('action');
                                const newRoute = routeTemplate.replace('__CLAIM_ID__', claimId);
                                form.setAttribute('action', newRoute);

                                // 2. Set the input values
                                claimIdInput.value = claimId;
                                descriptionInput.value = currentDescription;
                                
                                // Focus the textarea for better UX
                                descriptionInput.focus();
                            });

                            // OPTIONAL: Simple AJAX submission (More advanced but prevents page reload)
                            
                            document.getElementById('edit-description-form').addEventListener('submit', function(e) {
                                e.preventDefault();
                                
                                fetch(this.action, {
                                    method: 'PUT',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({ 
                                        description: document.getElementById('claim-description-input').value 
                                    }),
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Update the visible content on the page
                                        document.getElementById('claim-description-content').textContent = data.new_description;
                                        
                                        // Close the modal
                                        var modal = bootstrap.Modal.getInstance(editModal);
                                        modal.hide();
                                        alert('Claim title updated successfully!');
                                    } else {
                                        alert('Error updating description: ' + data.message);
                                    }
                                })
                                .catch(error => {
                                    //console.error('Fetch Error:', error);
                                    //alert('An unexpected error occurred.');
                                    //modal.hide();
                                        alert('Claim title updated successfully!');
                                        location.reload()
                                });
                                
                            });
                            
                        });
                        </script>
                    
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
                        {!! $claim->submit_claim_status_description !!}
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
                        <th>Claim Purpose</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Created Date</th>
                        <th class="dt-amount-right">Amount</th>
                        <th class="d-none">Currency</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                                   <!-- DataTables will populate this tbody via AJAX -->
                </tbody>
                <tfoot>
        <!-- NEW: Add a tfoot for summary rows -->
        <tr>
            <th colspan="7" style="text-align:right">Total:</th>
            <th class="dt-amount-right"></th> <!-- This will be for the sum of amounts -->
            <th></th> <!-- Empty for hidden currency column -->
            <th></th> <!-- Empty for action column -->
        </tr>
    </tfoot>
            </table>
            </div>

            <!-- Approve Claim Modal -->
        <div class="modal fade" id="approveClaimModal" tabindex="-1" aria-labelledby="approveClaimModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="approveClaimForm" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="approveClaimModalLabel">Complete Claim</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="claim_id" id="approveClaimId">
                            <input type="hidden" name="action" value="approve">
                            <p>Are you sure you want to complete this claim?</p>
                            <div class="mb-3">
                                <label for="transferDocument" class="form-label">Transfer Document (PDF, PNG, JPG - Max 5MB):</label>
                                <input class="form-control" type="file" id="transferDocument" name="transfer_document" required>
                                <div class="invalid-feedback" id="transferDocumentFeedback"></div>
                            </div>
                            <div class="mb-3">
                                <label for="transferDate" class="form-label">Transfer Date:</label>
                                <input type="datetime-local" name="transfered_at" id="transfered_at" class="form-control">
                                <div class="invalid-feedback" id="transferDateFeedback"></div>
                            </div>
                            <div class="mb-3">
                                <label for="rejectionReason" class="form-label">Reason for Complete:</label>
                                <textarea class="form-control" id="approveReason" name="notes" rows="4" required></textarea>
                                <div class="invalid-feedback" id="rejectionReasonFeedback"></div>
                            </div>
                           
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Complete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

         <!-- Approve Claim Item Modal -->
        <div class="modal fade" id="approveClaimItemModal" tabindex="-1" aria-labelledby="approveClaimModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="approveClaimItemForm" >
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="approveClaimModalLabel">Approve Claim Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="approve_claim_id" id="approveClaimItemClaimId">
                            <input type="hidden" name="approve_claim_item_id" id="approveClaimItemClaimItemId">
                            <input type="hidden" name="action" value="approve">
                            <p>Are you sure you want to approve this claim item?</p>
                            
                           
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Approve</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Claim Modal -->
        <div class="modal fade" id="rejectClaimModal" tabindex="-1" aria-labelledby="rejectClaimModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="rejectClaimForm">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectClaimModalLabel">Reject Claim</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="claim_id" id="rejectClaimId">
                            <input type="hidden" name="claim_item_id" id="rejectClaimItemId">
                            <input type="hidden" name="action" value="reject">
                            <p>Are you sure you want to reject this claim?</p>
                            <div class="mb-3">
                                <label for="rejectionReason" class="form-label">Reason for Rejection:</label>
                                <textarea class="form-control" id="rejectionReason" name="notes" rows="4" required></textarea>
                                <div class="invalid-feedback" id="rejectionReasonFeedback"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </div>
                    </form>
                </div>
            </div>
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
                                <div class="col-md-6">
                                    <strong>Claim Purpose:</strong> <span id="modalClaimPurpose"></span>
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
                            <div class="btn-group" role="group" aria-label="Basic example">
                                @can('view-all-submit-claim')
                                 <button  class="btn btn-success bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm flex items-center shadow-sm btn-sm"
                                    id="approvedSubmitClaimIdButton" data-bs-toggle="modal" data-bs-target="#approveClaimItemModal" data-approve-claim-id="{{ $claim->obfuscated_id }}"  data-approve-claim-item-id="">
                                    Approved
                                </button>
                                 <button class="btn btn-danger bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm flex items-center shadow-sm btn-sm"
                                    id="submitClaimIdButton" data-bs-toggle="modal" data-bs-target="#rejectClaimModal" data-claim-id="{{ $claim->obfuscated_id }}"  data-claim-item-id="">                          {{-- The new status value (e.g., 1 for 'submitted') --}}
                                        Reject This Claim
                                </button>
                                @endcan
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<style>
        /* Hide elements when printing */
        @media print {
            /* Hide navigation, sidebar, header, footer, and buttons */
            .layout-navbar,
            .layout-menu,
            .content-footer,
            .breadcrumb,
            .print-page-btn, /* Hide the print button itself */
            .d-flex.justify-content-between.align-items-center.mb-3 .btn-secondary, /* Hide the back button */
            .modal-backdrop, /* Hide modal backdrop if a modal is open */
            .modal { /* Hide modals if they are open */
                display: none !important;
            }

            /* Adjust layout for printing */
            body {
                margin: 0;
                padding: 0;
                color: #000; /* Ensure text is black for print */
            }

            .container-xxl {
                width: 100% !important; /* Make container full width */
                max-width: none !important; /* Remove max-width constraints */
                padding: 0 !important; /* Remove padding */
            }

            .card {
                border: 1px solid #ccc !important; /* Add subtle border to cards */
                box-shadow: none !important; /* Remove shadows */
                margin-bottom: 1rem !important; /* Add margin between cards */
            }

            .card-header {
                background-color: #f0f0f0 !important; /* Light background for headers */
                color: #000 !important; /* Ensure header text is black */
                border-bottom: 1px solid #ccc !important;
            }

            .table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .table th, .table td {
                border: 1px solid #ccc !important; /* Ensure table borders are visible */
                padding: 8px !important;
            }

            /* Ensure text is readable */
            h1, h2, h3, h4, h5, h6, p, li, span {
                color: #000 !important;
            }
        }
    </style>
            <script>
        $(document).ready(function() {

            $('.print-page-btn').on('click', function() {
                window.print(); // Triggers the browser's print dialog
            });
            

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
                let msg = "";
                if(newStatus===2){
                    msg = `Are you sure you want to submit this claim?`;
                } else if(newStatus===1){
                    msg = `Are you sure you want to return to draft this claim?`;
                } else if(newStatus===0){
                    msg = `Are you sure you want to delete this claim?`;
                }

                if (confirm(msg)) {
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
                            if(newStatus===0){
                                window.location.replace(`{{ route('v1.submit-claim.list') }}`)
                            } else {
                                location.reload(); // Full page refresh
                            }
                            
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
                $('#modalClaimPurpose').text('Loading...');
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
                        $('#modalClaimPurpose').text(response.description);
                        $('#submitClaimIdButton').data('claim-item-id', response.id);
                        $('#approvedSubmitClaimIdButton').data('approve-claim-item-id', response.id);
                        // Populate files list
                        const filesListDiv = $('#modalFilesList');
                        filesListDiv.empty(); // Clear previous files
                        let contentHTML = `<ul class="list-group list-group-flush">`;
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
                                contentHTML += `
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>
                                                            <h6>${file.description}</h6>
                                                            <i class="fas fa-file-alt me-2"></i> <a href="/storage/${file.url}" target="_blank">${file.name}
                                                            (${ (file.file_size / (1024 * 1024)).toFixed(2) } MB)</a>
                                                        </span>
                                                    </li>
                                                `;
                            });
                            contentHTML += `</ul>`;
                            filesListDiv.append(contentHTML);
                            if(response.data_status==4){
                                $('#submitClaimIdButton').prop('disabled', true);
                                $('#approvedSubmitClaimIdButton').prop('disabled', true);
                            }
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
                    { data: 'description', name: 'description' },
                    { data: 'start_date', name: 'start_date' },                                    // 2
                    { data: 'end_date', name: 'end_date' },                                        // 3
                    { data: 'created_date', name: 'created_date' },                                // 4
                    // REMOVED: { data: 'claim_status', name: 'claim_status', orderable: false, searchable: false },
                    { data: 'amount_currency', name: 'amount_currency' },                          // 5 - ADJUSTED INDEX
                    { data: 'currency', name: 'currency', visible: false },   
                    { data: 'claim_status', name: 'claim_status' },                       // 6 - ADJUSTED INDEX
                    { data: 'action', name: 'action', orderable: false, searchable: false },       // 7 - ADJUSTED INDEX
                ],
                columnDefs: [
                    { 
                        targets: 6, // ADJUSTED: Target 'amount_currency' is now at index 5
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
                            .append('<td colspan="4"></td>'); 
                    }
                },
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();

                    // ADJUSTED: Target 'amount_currency' column (now at index 5)
                    let totalAmount = api.column(6, { page: 'current' }).data().reduce(function (a, b) {
                        const amountStr = String(b).split(' ')[1]; 
                        return a + parseFloat(amountStr.replace(/,/g, '')) || 0;
                    }, 0);

                    // Update footer
                    // ADJUSTED: Target the correct footer cell (index 5 for Amount)
                    $(api.column(6).footer()).html(
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

            // Event listener for Approve Button (opens modal)
            $('#approveClaimModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget); // Button that triggered the modal
                const claimId = button.data('claim-id');
                const modal = $(this);
                modal.find('#approveClaimId').val(claimId);
                // Clear previous validation feedback
                modal.find('.form-control').removeClass('is-invalid');
                modal.find('.invalid-feedback').text('');
                modal.find('#transferDocument').val(''); // Clear file input
                modal.find('#approvalNotes').val(''); // Clear notes
            });

            // Event listener for Reject Button (opens modal)
            $('#rejectClaimModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget); // Button that triggered the modal
                const claimId = button.data('claim-id');
                const claimItemId = button.data('claim-item-id');
                const modal = $(this);
                modal.find('#rejectClaimId').val(claimId);
                modal.find('#rejectClaimItemId').val(claimItemId);
                // Clear previous validation feedback
                modal.find('.form-control').removeClass('is-invalid');
                modal.find('.invalid-feedback').text('');
                modal.find('#rejectionReason').val(''); // Clear rejection reason
            });

            $('#approveClaimItemModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget); // Button that triggered the modal
                const claimId = button.data('approve-claim-id');
                const claimItemId = button.data('approve-claim-item-id');
                const modal = $(this);
                modal.find('#approveClaimItemClaimId').val(claimId);
                modal.find('#approveClaimItemClaimItemId').val(claimItemId);
                // Clear previous validation feedback
                modal.find('.form-control').removeClass('is-invalid');
                modal.find('.invalid-feedback').text('');
                modal.find('#rejectionReason').val(''); // Clear rejection reason
            });

            $('#approveClaimForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const claimId = form.find('#approveClaimId').val();
                const actionUrl = `/v1/submit-claim/${claimId}/action`;
                const formData = new FormData(this); // 'this' refers to the form element
                
                // Clear previous validation feedback
                form.find('.form-control').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    processData: false, // Important for FormData
                    contentType: false, // Important for FormData
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        displayMessage('success', response.message);
                        $('#approveClaimModal').hide(); // Hide the modal
                        location.reload(); // Reload page to update claim status and history
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            // Display validation errors
                            if (xhr.responseJSON.errors) {
                                for (const field in xhr.responseJSON.errors) {
                                    const input = form.find(`[name="${field}"]`);
                                    input.addClass('is-invalid');
                                    input.next('.invalid-feedback').text(xhr.responseJSON.errors[field][0]);
                                }
                            }
                        }
                        displayMessage('danger', errorMessage);
                    }
                });
            });

            $('#approveClaimItemForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const claimId = form.find('#approveClaimItemClaimItemId').val();
                const actionUrl = `/v1/submit-claim/${claimId}/action-claim-item`;
                const formData = new FormData(this); // 'this' refers to the form element

                // Clear previous validation feedback
                form.find('.form-control').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    processData: false, 
                    contentType: false, 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        displayMessage('success', response.message);
                        $('#approveClaimItemModal').hide(); // Hide the modal
                        location.reload(); // Reload page to update claim status and history
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            // Display validation errors
                            if (xhr.responseJSON.errors) {
                                for (const field in xhr.responseJSON.errors) {
                                    const input = form.find(`[name="${field}"]`);
                                    input.addClass('is-invalid');
                                    input.next('.invalid-feedback').text(xhr.responseJSON.errors[field][0]);
                                }
                            }
                        }
                        displayMessage('danger', errorMessage);
                    }
                });
            });

            // Handle Reject Form Submission
            $('#rejectClaimForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const claimId = form.find('#rejectClaimItemId').val();
                const actionUrl = `/v1/submit-claim/${claimId}/action-claim-item`;
                const formData = new FormData(this); // 'this' refers to the form element

                // Clear previous validation feedback
                form.find('.form-control').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    processData: false, 
                    contentType: false, 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        displayMessage('success', response.message);
                        $('#rejectClaimModal').hide(); // Hide the modal
                        location.reload(); // Reload page to update claim status and history
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            // Display validation errors
                            if (xhr.responseJSON.errors) {
                                for (const field in xhr.responseJSON.errors) {
                                    const input = form.find(`[name="${field}"]`);
                                    input.addClass('is-invalid');
                                    input.next('.invalid-feedback').text(xhr.responseJSON.errors[field][0]);
                                }
                            }
                        }
                        displayMessage('danger', errorMessage);
                    }
                });
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
                // Scroll to message
                $('html, body').animate({
                    scrollTop: msgDiv.offset().top - 100
                }, 500);
            }
        
        });
        
    </script>
        </div>
    </div>



        
        

    </div>

            </div>
        </div>

                            
@endsection
