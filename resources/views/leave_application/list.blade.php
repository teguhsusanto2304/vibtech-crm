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
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>

        <!-- Card -->
        <div class="card">
            <div class="card-header text-white d-flex flex-wrap justify-content-between align-items-center">
                 @if(request()->routeIs('v1.submit-claim.all'))
                <div class="mb-2 mb-md-0">
                    <select class="form-control" id="userFilter" style="min-width: 180px;">
                        @php
                            $users = \App\Models\User::where('user_status',1)->get();
                        @endphp
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
            
            <div class="card-body" style="overflow-x: auto;">
                <ul class="nav nav-tabs mb-3" id="userStatusTabs" role="tablist">
                    
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ $defaultCountry === 'SG' ? 'active' : '' }}"
                            id="sg-tab"
                            data-bs-toggle="tab"
                            type="button"
                            role="tab"
                            data-status="SG"
                            aria-selected="{{ $defaultCountry === 'SG' ? 'true' : 'false' }}">
                            Singapore
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ $defaultCountry === 'MY' ? 'active' : '' }}"
                            id="my-tab"
                            data-bs-toggle="tab"
                            type="button"
                            role="tab"
                            data-status="MY"
                            aria-selected="{{ $defaultCountry === 'MY' ? 'true' : 'false' }}">
                            Malaysia
                        </button>
                    </li>
                    
                    
                </ul>
                <table class="table table-bordered table-striped nowrap w-100" id="submit-claim-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Country</th>
                            <th>Public Holiday Date</th>
                            <th>Title</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this tbody via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteClaimConfirmModal" tabindex="-1" aria-labelledby="deleteClaimConfirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteClaimConfirmModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this claim? This action cannot be undone.
                        <br><br>
                        <strong>Claim ID:</strong> <span id="confirmDeleteClaimId"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteClaimBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            $(document).ready(function() {
                // FIXED: Changed ID from #submit-claims-table to #submit-claim-table
                const mainClaimsDataTable = $('#submit-claim-table').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollX: true,
                    responsive: false, 
                    ajax: {
                        url: '{{ route("v1.leave-application.data") }}',
                        type: 'GET',
                        
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'country', name: 'country_code' },
                        { data: 'leave_date_formatted', name: 'leave_date' },
                        { data: 'title', name: 'title' },
                        { data: 'created_at_formatted', name: 'created_at' },
                        { data: 'action', orderable: false, searchable: false }
                    ],
                    columnDefs: [
                        {
                            targets: 5, // 'Items Summary' column (0-indexed)
                            className: 'dt-body-right dt-head-right' 
                        }
                    ],
                    order: [[ 0, 'asc' ]]
                });
                @if($defaultCountry === 'MY')
                    mainClaimsDataTable.column(1).search('MY').draw();
                @else                
                    mainClaimsDataTable.column(1).search('SG').draw();
                @endif
                $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                    const selectedStatus = $(e.target).data('status'); // Get 'active' or 'inactive' from data-status attribute
                    mainClaimsDataTable.column(1).search(selectedStatus).draw();
                    //$('#user_datatable').DataTable().ajax.reload(null, false); // Reload data, don't reset pagination
                });

                $('#userFilter').on('change', function () {
                    const selectedUser = $(this).val();
                    mainClaimsDataTable.column(8).search(selectedUser).draw();
                });

                let claimIdToDelete = null; // Variable to store the ID of the claim to be deleted

                // FIXED: Changed ID from #submit-claims-table to #submit-claim-table
                // Event listener for the "Delete" button in the main claims DataTable
                $('#submit-claim-table').on('click', '.delete-claim-btn', function(e) {
                    e.preventDefault(); 
                    claimIdToDelete = $(this).data('id'); // Get the obfuscated claim ID

                    if (!claimIdToDelete) {
                        console.error("Claim ID not found for delete button.");
                        alert("Error: Missing claim ID for deletion.");
                        return;
                    }

                    // Populate modal with the claim ID for confirmation
                    $('#confirmDeleteClaimId').text(claimIdToDelete);

                    // Show the confirmation modal
                    // Ensure Bootstrap 5 JS is loaded for this to work
                    const deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteClaimConfirmModal'));
                    deleteConfirmModal.show();
                });

                // Event listener for the "Delete" button INSIDE the confirmation modal
                $('#confirmDeleteClaimBtn').on('click', function() {
                    if (!claimIdToDelete) {
                        alert("Error: No claim selected for deletion.");
                        return;
                    }

                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    const deleteUrl = `/v1/submit-claim/${claimIdToDelete}/destroy`; // Use your Laravel DELETE route

                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE', // Use DELETE HTTP method
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            alert(response.message); // Show success message
                            
                            // Hide the modal
                            // Ensure Bootstrap 5 JS is loaded for this to work
                            const deleteConfirmModal = bootstrap.Modal.getInstance(document.getElementById('deleteClaimConfirmModal'));
                            if (deleteConfirmModal) {
                                deleteConfirmModal.hide();
                            }

                            // Reload the main claims DataTable
                            mainClaimsDataTable.ajax.reload(null, false); 
                        },
                        error: function(xhr, status, error) {
                            console.error("Error deleting claim:", status, error, xhr.responseText);
                            let errorMessage = 'Failed to delete claim.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            alert(errorMessage); // Show error message
                        },
                        complete: function() {
                            claimIdToDelete = null; // Clear the stored ID after operation
                        }
                    });
                });

                // Optional: Adjust columns on window resize (DataTables usually handles this, but good as a fallback)
                // $(window).on('resize', function () {
                //     mainClaimsDataTable.columns.adjust().draw();
                // });
            });
        </script>
        <script>
$(document).on('click', '.btn-delete', function () {
    const id = $(this).data('id');

    if (!id) {
        alert('Invalid ID');
        return;
    }

    if (!confirm('Are you sure you want to delete this leave?')) {
        return;
    }

    $.ajax({
        url: `/v1/leave-application/${id}/destroy`, // your delete route
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (res) {
            alert(res.message ?? 'Deleted successfully');

            // Reload DataTable if using DataTables
            $('#submit-claim-table').DataTable().ajax.reload(null, false);
        },
        error: function (xhr) {
            alert(xhr.responseJSON?.message || 'Failed to delete');
        }
    });
});
</script>

    </div>
@endsection
