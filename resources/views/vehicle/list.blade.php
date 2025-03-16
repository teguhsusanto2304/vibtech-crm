@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
            <!-- custom-icon Breadcrumb-->
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
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif


            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>


            <div class="card">
                <div class="card-header text-white d-flex flex-wrap justify-content-between align-items-center">
                    <div>  </div>
                    <a href="{{ route('v1.vehicles.create')}}" class="btn btn-primary">Add Vehicle</a>


                <div class="card-body">
                    <table class="table table-bordered table-striped" id="booking_datatable">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Car Plat Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <script type="text/javascript">
                $(document).ready(function () {
                    $(".cancel-booking").click(function () {
                        var bookingId = $(this).data("id");
                        var cancelUrl = "{{ route('v1.vehicle-bookings.cancel', ':id') }}".replace(':id', bookingId);
                        $("#cancelForm").attr("action", cancelUrl); // Ensure this updates correctly
                    });
                    var table = $('#booking_datatable').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('v1.vehicle.data') }}",
                        columns: [
                            {data: 'path_image', name: 'path_image', orderable: false, searchable: false },
                            { data: 'name', name: 'name' },
                            { data: 'action', name: 'action' }
                        ]
                    });

                });
            </script>
            <!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelConfirmModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Confirm Cancellation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel this vehicle booking?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                <form id="cancelForm" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-danger">Yes, Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>



@endsection
