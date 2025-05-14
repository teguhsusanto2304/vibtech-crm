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
        @if (session('errors'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('errors') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('v1.whistleblowing-policy.update') }}" method="POST">
                        @csrf
            <div class="col-md-12">
                <textarea id="features" name="whistleblowing_policy" class="form-control" rows="5">{{ $whistleblowningPolicyContent }}</textarea>
            </div>
            <div class="text-end mt-4">
                    <a href="{{ route('v1.whistleblowing-policy') }}" class="btn btn-danger">Cancel</a>&nbsp;
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const config = {
            ckfinder: {
                uploadUrl: '{{ route('ckeditor.upload') . '?_token=' . csrf_token() }}'
            }
        };

        // Create editors
        ['specifications', 'features'].forEach(function (id) {
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

@endsection
