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
                            <a href="javascript:void(0);">{{ $item }}</a>
                            <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                        </li>
                    @endforeach
                </ol>
            </nav>

            <h3>{{ $title }}</h3>
            <style>
                .btn_primary {
                    background-color: #003366;
                    color: #fff;
                }

                .btn_primary:hover {
                    background-color: #f0cf27;
                    color: #fff;
                }
            </style>
            <div class="tab-pane fade show active" id="btn-text-alignment-preview" role="tabpanel"
                aria-labelledby="btn-text-alignment-preview-tab">
                <div class="row gy-4">
                    <div class="col">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title">Create New</h5>
                                <p class="card-text">Create a New Job Assignment</p>
                                <a href="{{ route('v1.job-assignment-form.create')}}" class="btn btn_primary">Go Create New</a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title">View Job Assignment</h5>
                                <p class="card-text">Track Job Assignment Progress</p>
                                <a href="{{ route('v1.job-assignment-form.list')}}" class="btn btn_primary">Go View Job Assignment</a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title">Job Assignment History</h5>
                                <p class="card-text">Job Assignment History Records</p>
                                <a href="javascript:void(0);" class="btn btn_primary">Go Job Assignment History</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
@endsection
