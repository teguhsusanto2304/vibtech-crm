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

    <!-- Card -->
    <div class="card">
        <div class="card-header text-white d-flex flex-wrap justify-content-between align-items-center">
            <div>  </div>
            <a href="{{ route('v1.roles.create')}}" class="btn btn-primary">Add Role</a>
            <!-- Department Filter Box -->



        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped nowrap w-100" id="user_datatable">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Guard</th>
                        <th width="30px">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

   <script type="text/javascript">
     $(document).ready(function () {
      var table =  $('#user_datatable').DataTable({
           processing: true,
           serverSide: true,
           scrollX: true,
           ajax: "{{ route('v1.roles.data') }}",
           columns: [
               {data: 'name', name: 'name'},
               {data: 'guard_name', name: 'guard_name'},
               {data: 'action', name: 'action', orderable: false, searchable: false},
           ]
       });

     });
   </script>

@endsection
