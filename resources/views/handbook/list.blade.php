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
            @can('create-getting-started')
            <a href="{{ route('v1.getting-started.create')}}" class="btn btn-primary">Create New Employee Handbook</a>
            @endcan
            <!-- Department Filter Box -->
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($posts as $post)
                    @if((auth()->user()->can('v1.getting-started.destroy') && $post->data_status==0) || $post->data_status==1 )
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $post->title }}</h5>
                                <p class="card-text">{{ Str::limit($post->description, 100) }}</p>
                                <div class="mt-auto d-flex justify-content-center">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        @if($post->data_status==1)
                                            <a href="{{ route('v1.getting-started.read',['id'=>$post->id]) }}" class="btn btn-primary btn-sm">Read</a>
                                            @can('edit-getting-started')
                                                <a href="{{ route('v1.getting-started.edit',['id'=>$post->id]) }}" class="btn btn-info btn-sm">Edit</a>
                                            @endcan
                                            @can('destroy-getting-started')
                                            <form class="row g-3"
                                            action="{{ route('v1.getting-started.destroy', $post->id) }}"
                                            method="post">
                                            @csrf
                                            <button type="submit" class="btn btn-danger  btn-sm">Delete</button>
                                            @method('PUT')
                                                </form>
                                            @endcan
                                    @endif
                                    @if($post->data_status==0)
                                    @can('destroy-getting-started')
                                    <form class="row g-3"
                                    action="{{ route('v1.getting-started.destroy', $post->id) }}"
                                    method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-success  btn-sm">Restore</button>
                                    @method('PUT')
                                        </form>
                                    @endcan
                                    @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                <small>{{ $post->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @endif
                @empty
                    <p>No posts available.</p>
                @endforelse
            </div>

            <div class="d-flex justify-content-center">
                {!! $posts->links() !!}
            </div>
        </div>
    </div>



@endsection
