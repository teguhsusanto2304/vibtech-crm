@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
    <!-- custom-icon Breadcrumb-->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom-icon">
            @foreach ($breadcrumb as $item )
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">{{ $item }}</a>
                <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
            </li>
            @endforeach
        </ol>
    </nav>

    <h3>{{ $title }}</h3>


       <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
       <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
       <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
       <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>

    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <select class="form-control" id="departmentFilter" style="width: 200px;">
                <option value="">All Departments</option>
                <option value="Marketing">Marketing</option>
                <option value="Sales">Sales</option>
                <option value="Operations">Operations</option>
            <option value="IT Network">IT Network</option>
            <option value="System Projects">System Projects</option>

            </select>
            <button class="btn btn-primary">Add User</button>
            <!-- Department Filter Box -->

        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="user_datatable">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
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
           ajax: "{{ route('v1.users.data') }}",
           columns: [
               {data: 'department', name: 'department'},
               {data: 'name', name: 'name'},
               {data: 'email', name: 'email'},
               {data: 'action', name: 'action', orderable: false, searchable: false},
           ]
       });
       $('#departmentFilter').on('change', function () {
        const selectedDepartment = $(this).val();
        table.column(0).search(selectedDepartment).draw();
    });
     });
   </script>

@endsection
