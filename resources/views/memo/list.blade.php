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
            @can('create-management-memo')
            <a href="{{ route('v1.management-memo.create')}}" class="btn btn-primary">Create New Management Memo</a>
            @endcan
            <!-- Department Filter Box -->
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="managementMemoTable" class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="60%">Title</th>
                            <th width="5%">Read</th>
                            <th width="5%">Unread</th>
                            <th width="20%">Created</th>
                            <th width="10%">Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            @if((auth()->user()->can('view-management-memo') && $post->data_status==0) || $post->data_status==1)
                                <tr>
                                    <td>{{ $post->title }}</td>
                                    <td>{{ $post->userRead->count() }}</td>
                                    <td>{{ $totalRelevantUsersCount - $post->userRead->count() }} </td>
                                    <td><small>{{ $post->created_at->format('d M Y') }}</small>
                                    <br><small>{{ $post->user->name }}</small></td>
                                    <td>
                                        @if($post->data_status == 1)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Archive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('v1.management-memo.read',['id'=>$post->id]) }}" class="btn btn-primary btn-sm" title="Read">
                                                Read
                                            </a>

                                            @if($post->data_status == 1)
                                                @can('edit-getting-started')
                                                    <a href="{{ route('v1.management-memo.edit',['id'=>$post->id]) }}" class="btn btn-info btn-sm" title="Edit">
                                                        Edit
                                                    </a>
                                                @endcan
                                                @can('destroy-getting-started')
                                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal{{ $post->id }}">
                                                        Archive
                                                    </button>
                                                @endcan
                                            @endif

                                            @if($post->data_status == 0)
                                                @can('destroy-getting-started')
                                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#confirmRestoreModal{{ $post->id }}">
                                                        Restore
                                                    </button>

                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmPermanentDeleteModal{{ $post->id }}">
                                                        Delete
                                                    </button>
                                                @endcan
                                            @endif
                                        </div>

                                        {{-- Include your Modals here for each action --}}
                                        @include('memo.modal.delete', ['post' => $post])
                                        @include('memo.modal.restore', ['post' => $post])
                                        @include('memo.modal.permanent-delete', ['post' => $post])

                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No posts available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

  <script>
    $(document).ready(function () {
        $('#managementMemoTable').DataTable({
            responsive: true,
            pageLength: 10, // show 10 rows per page
            lengthChange: true, // show "Show 10/25/50/All" dropdown
            ordering: true, // allow sorting
            order: [[1, 'asc']],
        });
    });
</script>


@endsection
