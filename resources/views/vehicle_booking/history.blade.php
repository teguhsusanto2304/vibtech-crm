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


                <div class="card-body">
                    <table class="table table-bordered table-striped" id="booking_datatable">
                        <thead>
                            <tr>
                                <th>Car</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Purposes</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <x-toast-notification />
            <x-booking-modal />

            <script type="text/javascript">
                let statusSuccess = 0;
                $(document).ready(function () {
                    var table = $('#booking_datatable').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('v1.vehicle-bookings.histories-data') }}",
                        columns: [
                            { data: 'name', name: 'name' },
                            { data: 'start_at_formatted', name: 'start_at_formatted' },
                            { data: 'end_at_formatted', name: 'end_at_formatted' },
                            { data: 'purposes', name: 'purposes' },
                            { data: 'action', name: 'action' }
                        ]
                    });
                    window.addEventListener("reloadDataTable", function () {
                        table.ajax.reload(null, false); // 🔄 Reload table without resetting pagination
                    });

                });

            </script>
            <x-booking-cancel />

@endsection
