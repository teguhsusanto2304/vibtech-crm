@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- custom-icon Breadcrumb-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom-icon">
                    @foreach ($breadcrumb as $item)
                        <li class="breadcrumb-item">
                            @if($item=='Job Assignment Form')
                                <a href="{{ route('v1.job-assignment-form')}}">{{ $item }}</a>
                            @else
                                <a href="javascript:void(0);">{{ $item }}</a>
                            @endif
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

            <div class="nav-align-top nav-tabs-shadow mb-6">

                <ul class="nav nav-tabs nav-fill" role="tablist">
                    <li class="nav-item">
                        <button
                          type="button"
                          class="nav-link active"
                          role="tab"
                          data-bs-toggle="tab"
                          data-bs-target="#navs-justified-job-assignment-list"
                          aria-controls="navs-justified-job-assignment-list"
                          aria-selected="false">
                          <span class="d-none d-sm-block"
                            ><i class="tf-icons bx bx-user bx-sm me-1_5 align-text-bottom"></i> Assign By You</span
                          ><i class="bx bx-user bx-sm d-sm-none"></i>
                        </button>
                      </li>
                    <li class="nav-item">
                    <button
                      type="button"
                      class="nav-link"
                      role="tab"
                      data-bs-toggle="tab"
                      data-bs-target="#navs-justified-assigned-by-you"
                      aria-controls="navs-justified-assigned-by-you"
                      aria-selected="true">
                      <span class="d-none d-sm-block"
                        ><i class="tf-icons bx bx-user bx-sm me-1_5 align-text-bottom"></i> Assigned To You
                        <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-danger ms-1_5 pt-50"
                          >3</span
                        ></span
                      ><i class="bx bx-home bx-sm d-sm-none"></i>
                    </button>
                  </li>

                </ul>
                <div class="tab-content">
                  <div class="tab-pane fade show active" id="navs-justified-job-assignment-list" role="tabpanel">
                    <h3>Assigned By You</h3>
                    <select class="form-control mb-5" id="departmentFilter" style="width: 200px;">
                        <option value="">All Status</option>
                        <option value="0">Pending</option>
                        <option value="1">Accepted</option>
                        <option value="2">Rejected</option>
                          </select>
                    <table class="table table-bordered table-striped" id="job_datatable" width="100%">
                        <thead>
                            <tr>
                                <th>Job Record ID</th>
                                <th>Job Created Date</th>
                                <th>Type of Job</th>
                                <th>Business Name</th>
                                <th>Date of Job</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>

                  </div>

                  <div class="tab-pane fade" id="navs-justified-assigned-by-you" role="tabpanel">
                    <h3>Assigned To You</h3>
                    <table class="table table-bordered table-striped" id="job_datatable1" width="100%">
                        <thead>
                            <tr>
                                <th>Job Record ID</th>
                                <th>Job Created Date</th>
                                <th>Type of Job</th>
                                <th>Business Name</th>
                                <th>Date of Job</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                  </div>

                </div>
              </div>
              <script type="text/javascript">
                $(document).ready(function () {
                 var table =  $('#job_datatable').DataTable({
                      processing: true,
                      serverSide: true,
                      ajax: "{{ route('v1.job-assignment-form.job-list') }}",
                      columns: [
                          {data: 'job_record_id', name: 'job_record_id'},
                          {data: 'created_date', name: 'created_date'},
                          {data: 'job_type_name', name: 'job_type_name'},
                          {data: 'business_name', name: 'business_name'},
                          {data: 'date_range', name: 'date_range'},
                          {data: 'status', name: 'status'},
                          {data: 'action', name: 'action', orderable: false, searchable: false},
                          {data: 'job_status', name: 'job_status',visible: false},
                      ]
                  });
                  $('#departmentFilter').on('change', function () {
                   const selectedDepartment = $(this).val();
                   table.column(7).search(selectedDepartment).draw();
               });
            });
            </script>
              <script type="text/javascript">
                $(document).ready(function () {
                 var table =  $('#job_datatable1').DataTable({
                      processing: true,
                      serverSide: true,
                      ajax: "{{ route('v1.job-assignment-form.job-list-user') }}",
                      columns: [
                          {data: 'job_record_id', name: 'job_record_id'},
                          {data: 'created_date', name: 'created_date'},
                          {data: 'job_type_name', name: 'job_type_name'},
                          {data: 'business_name', name: 'business_name'},
                          {data: 'date_range', name: 'date_range'},
                          {data: 'status', name: 'status'},
                          {data: 'action', name: 'action', orderable: false, searchable: false},
                          {data: 'job_status', name: 'job_status',visible: false},
                      ]
                  });
                  $('#departmentFilter').on('change', function () {
                   const selectedDepartment = $(this).val();
                   table.column(7).search(selectedDepartment).draw();
               });
            });
            </script>

        </div>
    </div>
@endsection
