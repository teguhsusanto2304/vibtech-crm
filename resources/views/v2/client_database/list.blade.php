@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>


<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
<style> 
.remarks-scroll-container {
    max-height: 100px; /* Adjust this value as needed */
    overflow-y: auto;  /* Enables vertical scrolling if content exceeds max-height */
    padding-right: 5px; /* Adds a little space for the scrollbar */
    /* Optional styling for better appearance */
    border: 1px solid #eee; /* Light border */
    background-color: #f9f9f9; /* Light background */
    margin: 0; /* Remove default margins from paragraphs if any */
}

/* Optional: Style for the paragraphs inside to reduce spacing if needed */
.remarks-scroll-container p {
    margin-bottom: 5px; /* Reduce space between remarks */
    padding: 0;
}

.remarks-scroll-container p:last-child {
    margin-bottom: 0; /* No margin on the last paragraph */
}
</style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <x-page-header
            title="Client Database List" {{-- Pass the title as a string --}}
            :breadcrumb='$breadcrumb'
        />
        <!-- DataTable Dependencies -->       

        <!-- Card -->
        <div class="card">
            <div class="card-header text-white d-flex flex-wrap justify-content-between align-items-center">
                <div></div>
                {{-- Call your new component here --}}
                <x-client-filter-form :salesPersons="$salesPersons" :industries="$industries" :countries="$countries" :downloadFile="$downloadFile" />
            </div>
            <div class="card-body" style="overflow-x: auto;">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped nowrap w-100" id="clients-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Email</th>
                                <th>Office Number</th>
                                <th>Mobile Number</th>
                                <th>Job Title</th>
                                <th>Industry</th>
                                <th>Country</th>
                                <th>Sales Person</th>
                                @can('edit-reasign-salesperson')
                                <th>Reassign Sales Person</th>
                                @endcan
                                <th>Image</th>
                                <th>Created On</th>
                                <th>Updated On</th>
                                <th>Action</th>
                                <th>Quotation</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Email</th>
                                <th>Office Number</th>
                                <th>Mobile Number</th>
                                <th>Job Title</th>
                                <th>Industry</th>
                                <th>Country</th>
                                <th>Sales Person</th>
                                @can('edit-reasign-salesperson')
                                <th>Reassign Sales Person</th>
                                @endcan
                                <th>Image</th>
                                <th>Created On</th>
                                <th>Updated On</th>
                                <th>Action</th>
                                <th>Quotation</th>
                                <th>Remarks</th>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="mb-3">
                        <div class="btn-group" role="group" aria-label="Basic example">
                        @can('edit1-client-database')
                            <button id="reassign-selected" class="btn btn-success">Reassign All</button>
                        @endcan
                            <button id="edit-selected" class="btn btn-primary">Request to edit All</button>
                        @can('delete-client-database')
                            <button id="delete-selected" class="btn btn-danger">Delete All</button>
                        @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $('#reset-filters').on('click', function () {
                $('#filter-sales-person').val('');
                $('#filter-industry').val('');
                $('#filter-country').val('');
                $('#clients-table').DataTable().ajax.reload();
            });

            $(function () {
                let table = $('#clients-table').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollX: true, // Enable horizontal scrolling
                    responsive: false,
                    ajax: {
                        url: '{{ route('v2.client-database.data') }}',
                        data: function (d) {
                            d.sales_person = $('#filter-sales-person').val();
                            d.industry = $('#filter-industry').val();
                            d.country = $('#filter-country').val();
                        }
                    },

                    columns: [
                        { data: 'id',
                          orderable: false,
                          searchable: false,
                            render: function (data, type, row) {
                                return `<input type="checkbox" class="row-checkbox" value="${data}" data-editable="${row.is_editable}">`;
                            }
                        },
                        { data: 'name' },
                        { data: 'company' },
                        { data: 'email' },
                        { data: 'office_number' },
                        { data: 'mobile_number' },
                        { data: 'job_title' },
                        { data: 'industry', name: 'industryCategory.name' },
                        { data: 'country', name: 'country.name' },
                        { data: 'salesPerson', name: 'salesPerson.name' },
                        @can('edit-reasign-salesperson')
                        { data: 'sales_person_btn' },
                        @endcan
                        {
                            data: 'image_path_img',
                            render: function (data, type, row) {
                                if (type === 'display') {
                                    if (data === null) {
                                        return '<svg fill="#000000" width="80px" height="80px" viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:none;}</style></defs><title>no-image</title><path d="M30,3.4141,28.5859,2,2,28.5859,3.4141,30l2-2H26a2.0027,2.0027,0,0,0,2-2V5.4141ZM26,26H7.4141l7.7929-7.793,2.3788,2.3787a2,2,0,0,0,2.8284,0L22,19l4,3.9973Zm0-5.8318-2.5858-2.5859a2,2,0,0,0-2.8284,0L19,19.1682l-2.377-2.3771L26,7.4141Z"/><path d="M6,22V19l5-4.9966,1.3733,1.3733,1.4159-1.416-1.375-1.375a2,2,0,0,0-2.8284,0L6,16.1716V6H22V4H6A2.002,2.002,0,0,0,4,6V22Z"/><rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1" width="32" height="32"/></svg>';
                                    } else {
                                        return `
                                    <img src="${data}"
                                         alt="User Image"
                                         width="80" height="80"
                                         class="img-thumbnail preview-image"
                                         data-bs-toggle="modal"
                                         data-bs-target="#imageModal"
                                         style="cursor:pointer"
                                         data-full="${data}">
                                    <p><a href="${data}" download><small>Download</small></a></p>
                                `;
                                    }

                                }
                                return data; // Untuk sorting, filtering, dll. tetap gunakan data asli
                            }
                        },
                        { data: 'created_on' },
                        { data: 'updated_on' },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                        { data: 'quotation', name: 'quotation' },
                        { data:'remarks', name:'remarks'}
                    ]
                });  
                
                $('#filter-sales-person, #filter-industry, #filter-country').on('change', function () {
                    table.ajax.reload();
                });
            });
        </script>

        <!-- Generic Message Modal -->
<div class="modal fade" id="messageAlertModal" tabindex="-1" aria-labelledby="messageAlertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageAlertModalLabel">Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="messageAlertModalBody">
                <!-- Message content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Generic Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmationModalBody">
                <!-- Message content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmActionBtn">Yes</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to show a custom modal alert
    function showCustomAlert(message, title = 'Notification') {
        $('#messageAlertModalLabel').text(title); // Set the modal title
        $('#messageAlertModalBody').html(`<p>${message}</p>`); // Set the modal body message
        $('#messageAlertModal').modal('show'); // Show the modal
    }

    let confirmationCallback = null;

    // Function to show a custom confirmation modal
    function showConfirmationModal(message, title = 'Confirm Action', callback) {
        $('#confirmationModalLabel').text(title); // Set the modal title
        $('#confirmationModalBody').html(`<p>${message}</p>`); // Set the modal body message

        // Store the callback function
        confirmationCallback = callback;

        // Show the modal
        $('#confirmationModal').modal('show');
    }

    $(document).ready(function() {
        $('#confirmActionBtn').off('click').on('click', function() {
            $('#confirmationModal').modal('hide'); // Hide the confirmation modal

            if (confirmationCallback && typeof confirmationCallback === 'function') {
                confirmationCallback(); // Execute the stored callback function
            }
            confirmationCallback = null; // Clear the callback after execution
        });

        // Also clear the callback if the modal is dismissed without confirming
        $('#confirmationModal').on('hidden.bs.modal', function () {
            confirmationCallback = null;
        });
    });
</script>


        <x-client-detail />

        <x-client-delete />

        <x-client-assignment-sales-person />
        
        <x-datatable-select-all />

        <x-datatable-assign-all />

        <x-datatable-delete-all />

        <x-datatable-request-edit-all />

        

        <!-- Image Preview Modal -->
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <img src="" id="modalImage" class="img-fluid" alt="Full Size">
                    </div>
                </div>
            </div>
        </div>
@endsection

    @push('scripts')

    @endpush
