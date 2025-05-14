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
    <div class="card shadow-sm border-0">
        <div class="card-body">
    <div class="mb-4">
    @can('create-whistleblowing-policy')
    @if(empty($whistleblowningPolicyContent))
        <a href="{{ route('v1.whistleblowing-policy.create') }}" class="btn btn-primary">Create</a>
    @else

        <div class="btn-group" role="group" aria-label="Basic mixed styles example">
            <a href="{{ route('v1.whistleblowing-policy.edit') }}" class="btn btn-info">Edit</a>
            <a href="javascript:void(0);" class="btn btn-danger delete-button"
            data-route="{{ route('v1.whistleblowing-policy.destroy') }}">
                Delete
            </a>
            <form id="delete-form" action="" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-button');
    const deleteForm = document.getElementById('delete-form');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default link behavior

            const route = this.dataset.route;

            if (confirm('Are you sure you want to delete this policy? This action cannot be undone.')) {
                deleteForm.action = route; // Set the form's action to the delete route
                deleteForm.submit(); // Submit the form
            }
        });
    });
});
            </script>
        </div>
    @endif
    @endcan
    </div>

    {!! $whistleblowningPolicyContent !!}
    @if(!empty($whistleblowningPolicyCreatedAt))
        <p>Policy Last Created on <strong>{{ $whistleblowningPolicyCreatedAt }}</strong> | Created by <strong>{{ $whistleblowningPolicyCreatedBy }}</strong><p>
    @endif
    @if(!empty($whistleblowningPolicyUpdatedAt))
        <p>Policy Last Updated on <strong>{{ $whistleblowningPolicyUpdatedAt }}</strong> | Updated by <strong>{{ $whistleblowningPolicyUpdatedBy }}</strong><p>
    @endif
    </div>
    </div>
    @cannot('create-whistleblowing-policy')
    <div class="card shadow-sm border-0 mt-4" >
        <div class="card-body">
            @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Whoops! Something went wrong.</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
            <h3 class="section-title mb-4">Report an Incident</h3>
            <p class="lead mb-3">Please describe the incident you want to report below. Your identity will be kept strictly confidential.</p>

            <form class="form-container" method="POST" action="{{ route('v1.whistleblowing-policy.report')}}">
                @csrf
                <div class="mb-3">
                    <label for="description" class="form-label">Description of Incident:</label>
                    <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>
<script>
        document.addEventListener("DOMContentLoaded", function () {
    const config = {
        toolbar: [
            'bold',
            'italic',
            'link',
            'bulletedList',
            'numberedList',
            '|',
            'undo',
            'redo'
        ],
        // Remove the ckfinder configuration to disable image uploads
        // ckfinder: {
        //     uploadUrl: '{{ route('ckeditor.upload') . '?_token=' . csrf_token() }}'
        // }
    };

    // Create editors
    ['description'].forEach(function (id) {
        ClassicEditor.create(document.querySelector('#' + id), config)
            .then(editor => {
                // Set height after initialization
                editor.editing.view.change(writer => {
                    writer.setStyle('min-height', '300px', editor.editing.view.document.getRoot());
                });
            })
            .catch(error => {
                console.error(error);
            });
    });
});
    </script>
    @endcannot


@endsection
