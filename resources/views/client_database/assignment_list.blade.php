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

            <!-- Success Message -->
            @if (session('errors'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('errors') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        <!-- DataTable Dependencies -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
        <!-- Buttons CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">



        <!-- Card -->
        <div class="card">
            <div class="card-header text-white d-flex flex-wrap align-items-center">
                <div></div>


               {{-- Call your new component here --}}
                <x-sales-person-filter :salesPersons="$salesPersons" salesPersonTitle="Recommended For"/>

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
                                <th>Image</th>
                                <th>Contact Uploaded By</th>
                                <th>Recommended For</th>
                                <th>Assign To</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="mb-3">
                                <button id="approve-selected" class="btn btn-success">Assign To All</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="clientDetailModal" tabindex="-1" aria-labelledby="clientDetailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clientDetailModalLabel">Client Detail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="client-detail-body">
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="clientDetailAssignModal" tabindex="-1" aria-labelledby="clientDetailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clientDetailModalLabel">Salesperson Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="assignClientForm" method="POST"
                        action="{{ route('v1.client-database.assignment-salesperson')}}">
                        @csrf
                        @METHOD('PUT')
                        <div class="modal-body" id="client-detail-assign-body">
                            <p>Loading...</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                                aria-label="Close">No</button>&nbsp;
                            <button type="submit" class="btn btn-success">Yes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="clientDetailBulkAssignModal" tabindex="-1" aria-labelledby="clientDetailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clientDetailModalLabel">Bulk Salesperson Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="assignClientForm" method="POST"
                        action="{{ route('v1.client-database.bulk-assignment-salesperson')}}">
                        @csrf
                        @METHOD('PUT')
                        <div class="modal-body" id="client-detail-bulk-assign-body">
                            <p>Loading...</p>
                        </div>
                        <input type="hidden" name="client_ids" id="client_ids">
                        <input type="hidden" name="status" value="assign">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                                aria-label="Close">No</button>&nbsp;
                            <button type="submit" class="btn btn-success">Yes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to <span id="actionText"></span> this user?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Modal -->
        <div class="modal fade" id="loginConfirmModal" tabindex="-1" aria-labelledby="loginConfirmLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="loginConfirmForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Your Identity</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="loginEmail" name="email"
                                    value="{{ auth()->user()->email }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="loginPassword" name="password" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Confirm</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            let assignForm;



                $(document).on("submit", "#assignClientForm", function (e) {
                    e.preventDefault(); // Stop normal form submission
                    assignForm = this; // Save form reference
                    $('#clientDetailAssignModal').modal('hide');
                    $("#loginConfirmModal").modal("show"); // Show the login modal
                });
                $("#loginConfirmForm").on("submit", function (e) {
                    e.preventDefault();

                    $.ajax({
                        url: "{{ route('v1.login.verify') }}", // Adjust to your login check route
                        method: "POST",
                        data: $(this).serialize(),
                        success: function (res) {
                            if (res.success) {
                                $("#loginConfirmModal").modal("hide");

                                // Submit the original form after login verified
                                assignForm.submit();
                            } else {
                                alert("Login failed. Please try again.");
                            }
                        },
                        error: function () {
                            alert("An error occurred while verifying login.");
                        }
                    });
                });


                $('#reset-filters').on('click', function () {
                $('#filter-sales-person').val('');
                $('#filter-industry').val('');
                $('#filter-country').val('');
                $('#clients-table').DataTable().ajax.reload();
            });
            $('#download-csv').on('click', function () {
                const params = new URLSearchParams({
                    sales_person: $('#filter-sales-person').val(),
                    industry: $('#filter-industry').val(),
                    country: $('#filter-country').val()
                });
                window.location.href = `/v1/client-database/export/csv?${params.toString()}`;
            });

            $('#download-pdf').on('click', function () {
                const params = new URLSearchParams({
                    sales_person: $('#filter-sales-person').val(),
                    industry: $('#filter-industry').val(),
                    country: $('#filter-country').val()
                });
                window.location.href = `/v1/client-database/export/pdf?${params.toString()}`;
            });
            $(function () {
                let table = $('#clients-table').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollX: true, // Enable horizontal scrolling
                    responsive: false,
                    ajax: {
                        url: '{{ route('v1.client-database.assignment-salesperson.data') }}',
                        data: function (d) {
                            d.sales_person = $('#filter-sales-person').val();
                        }
                    },

                    columns: [
                        {
                                        data: 'id',
                                        orderable: false,
                                        searchable: false,
                                        render: function (data, type, row) {
                                            return `<input type="checkbox" class="row-checkbox" value="${data}">`;
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
                        { data: 'contact_for' },
                        { data: 'assign_to' },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    ]
                });

                function getSelectedIds() {
                    return $('.row-checkbox:checked').map(function () {
                        return this.value;
                    }).get();
                }

                $('#select-all').on('click', function() {
                    const isChecked = this.checked;
                    // Select/Deselect all visible row checkboxes
                    $('.row-checkbox').prop('checked', isChecked);

                    // Update the selectedClientIds map for the current page
                    table.rows({ page: 'current' }).data().each(function(row) {
                        if (isChecked) {
                            selectedClientIds[row.id] = true;
                        } else {
                            delete selectedClientIds[row.id];
                        }
                    });
                });

                $('#approve-selected').on('click', function () {
                    let ids = getSelectedIds();
                    if (ids.length === 0) {
                        alert('Please select at least one row to start assignment.');
                        return;
                    }
                    if (confirm('Are you sure you want to assignment the selected data?')) {
                        handleBulkAction(ids, 'approve');
                    }
                });

                function handleBulkAction(ids, action) {
                    $('#client-detail-assign-body').html('<p>Loading...</p>');
                    $('#clientDetailBulkAssignModal').modal('show');

                    $.get('/v1/client-database/0/detail', function (data) {
                        $('#client-detail-bulk-assign-body').html(data);
                        $('#client_ids').val(ids);
                    }).fail(function () {
                        $('#client-detail-assign-body').html('<p class="text-danger">Failed to load client data.</p>');
                    });
                    }

                    $('#filter-sales-person, #filter-industry, #filter-country').on('change', function () {
                        table.ajax.reload();
                    });


                $('#filter-sales-person, #filter-industry, #filter-country').on('change', function () {
                    table.ajax.reload();
                });

                $(document).on('click', '.view-client', function () {
                    const clientId = $(this).data('id');
                    $('#client-detail-body').html('<p>Loading...</p>');
                    $('#clientDetailModal').modal('show');

                    $.get('/v1/client-database/' + clientId + '/detail', function (data) {
                        $('#client-detail-body').html(data);
                    }).fail(function () {
                        $('#client-detail-body').html('<p class="text-danger">Failed to load client data.</p>');
                    });
                });

                $(document).on('click', '.view-client-assign', function () {
                    const clientId = $(this).data('id');
                    $('#client-detail-assign-body').html('<p>Loading...</p>');
                    $('#clientDetailAssignModal').modal('show');

                    $.get('/v1/client-database/' + clientId + '/detail?assign=yes', function (data) {
                        $('#client-detail-assign-body').html(data);
                        $('#client_id').val(clientId);
                    }).fail(function () {
                        $('#client-detail-assign-body').html('<p class="text-danger">Failed to load client data.</p>');
                    });
                });


                $(document).on("click", ".confirm-action", function () {
                    userId = $(this).data("id");
                    actionType = $(this).data("action");

                    // Set action text in modal
                    $("#actionText").text(actionType === "deactivate" ? "deactivate" : "activate");

                    // Show modal
                    $("#confirmModal").modal("show");
                });
                $("#confirmBtn").on("click", function () {
                    $.ajax({
                        url: "{{ route('v1.client-database.toggle-status') }}", // Your Laravel route
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: userId,
                            action: actionType
                        },
                        success: function (response) {
                            $("#confirmModal").modal("hide"); // Close modal
                            if (response.success) {
                                //alert("User " + actionType + "d successfully!");
                                location.reload(); // Refresh DataTables
                            } else {
                                alert("Failed to " + actionType + " user.");
                            }
                        },
                        error: function () {
                            alert("An error occurred.");
                        }
                    });
                });

                $(document).on('click', '.preview-image', function () {
                    const fullImg = $(this).data('full');
                    $('#modalImage').attr('src', fullImg);
                });
            });
        </script>
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
