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
        </div>

        <!-- DataTable Dependencies -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
        <!-- Buttons CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">



        <!-- Card -->
        <div class="card">
            <div class="card-header text-white d-flex flex-wrap justify-content-between align-items-center">
                <div> </div>
                <!-- Department Filter Box -->
                <form id="filters-form" class="d-flex gap-2 flex-wrap align-items-center">
                    <select id="filter-sales-person" class="form-select" style="width: 200px;">
                        <option value="">All Sales Persons</option>
                        @foreach ($salesPersons as $salesPerson)
                            <option value="{{ $salesPerson->name }}">{{ $salesPerson->name }}</option>
                        @endforeach
                    </select>

                    <select id="filter-industry" class="form-select" style="width: 200px;">
                        <option value="">All Industries</option>
                        @foreach ($industries as $industry)
                            <option value="{{ $industry->name }}">{{ $industry->name }}</option>
                        @endforeach
                    </select>

                    <select id="filter-country" class="form-select" style="width: 200px;">
                        <option value="">All Countries</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->name }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </form>

            </div>
            <div class="card-body" style="overflow-x: auto;">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped nowrap w-100" id="clients-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Email</th>
                                <th>Office Number</th>
                                <th>Mobile Number</th>
                                <th>Job Title</th>
                                <th>Industry</th>
                                <th>Country</th>
                                <th>Sales Person</th>
                                <th>Image</th>
                                <th>Quotation</th>
                                <th>Requested On</th>
                                <th>Updated On</th>
                                <th>Request</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
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

        <div class="modal fade" id="clientDetailDeleteModal" tabindex="-1" aria-labelledby="clientDetailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clientDetailModalLabel">Client Detail Delete Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="deleteClientForm" method="POST" action="{{ route('v1.client-database.update-request')}}">
                    @csrf
                    <div class="modal-body" id="client-detail-delete-body">
                        <p>Loading...</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Delete</button>
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
                        Are you sure you want to <span id="actionText"></span> this client?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Include jQuery + DataTables -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
        <!-- Buttons JS + Dependencies -->
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
        <script>
            $(function () {
                let table = $('#clients-table').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollX: true, // Enable horizontal scrolling
                    responsive: false,
                    ajax: {
                        url: '{{ route('v1.client-database.data-request') }}',
                        data: function (d) {
                            d.sales_person = $('#filter-sales-person').val();
                            d.industry = $('#filter-industry').val();
                            d.country = $('#filter-country').val();
                        }
                    },
                    dom: 'Bfrtip', // Add buttons to top
                    buttons: [
                        {
                            extend: 'csvHtml5',
                            title: 'Client_List',
                            text: 'Download CSV',
                            exportOptions: {
                                columns: ':not(:last-child):not(:nth-child(10))' // exclude Action and Image
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            title: 'Client_List',
                            text: 'Download PDF',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: ':not(:last-child):not(:nth-child(10))' // exclude Action and Image
                            }
                        }
                    ],
                    columns: [
                        { data: 'name' },
                        { data: 'company' },
                        { data: 'email' },
                        { data: 'office_number' },
                        { data: 'mobile_number' },
                        { data: 'job_title' },
                        { data: 'industry', name: 'industryCategory.name' },
                        { data: 'country', name: 'country.name' },
                        { data: 'sales_person', name: 'salesPerson.name' },
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
                        { data: 'quotation', name: 'quotation' },
                        { data: 'created_by' },
                        { data: 'updated_on' },
                        { data: 'request_status', name: 'request_status', orderable: false, searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    ]
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

                $(document).on('click', '.view-client-delete', function () {
                    const clientId = $(this).data('id');
                    $('#client-detail-delete-body').html('<p>Loading...</p>');
                    $('#clientDetailDeleteModal').modal('show');

                    $.get('/v1/client-database/' + clientId + '/detail?delete=yes', function (data) {
                        $('#client-detail-delete-body').html(data);
                    }).fail(function () {
                        $('#client-detail-delete-body').html('<p class="text-danger">Failed to load client data.</p>');
                    });
                });


                $(document).on("click", ".confirm-action", function () {
                    userId = $(this).data("id");
                    actionType = $(this).data("action");

                    // Set action text in modal
                    $("#actionText").text(actionType === "delete" ? "Delete" : "activate");

                    // Show modal
                    $("#confirmModal").modal("show");
                });
                $("#confirmBtn").on("click", function () {
                    $.ajax({
                        url: "{{ route('v1.client-database.delete-request') }}", // Your Laravel route
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: userId,
                            action: 3
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
