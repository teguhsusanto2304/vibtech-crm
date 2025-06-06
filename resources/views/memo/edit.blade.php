@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Custom Icon Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom-icon">
                    @foreach ($breadcrumb as $item)
                        <li class="breadcrumb-item">
                            @if($item == 'Job Assignment Form')
                                <a href="{{ route('v1.job-assignment-form') }}">{{ $item }}</a>
                            @else
                                <a href="javascript:void(0);">{{ $item }}</a>
                            @endif
                            <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                        </li>
                    @endforeach
                </ol>
            </nav>

            <h3>{{ $title }}</h3>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Oops! Something went wrong:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form class="row g-3"
                        action="{{ route('v1.management-memo.update', $post->id) }}"
                        method="post">
                        @csrf
                        @method('PUT') {{-- Laravel requires this for update operations --}}

                        <div class="col-md-8">
                            <label for="roleName" class="form-label">Title</label>
                            <input type="text" class="form-control" name="title"
                                value="{{ old('title', $post->title) }}" {{-- Pre-fill with role name --}}
                                placeholder="Enter title">
                        </div>

                        <div class="col-md-12" style="display: none;">
                            <label for="staffName" class="form-label">Description</label>
                            <input type="text" class="form-control" name="description" value="{{ old('description',$post->description) }}"
                                placeholder="Enter description">
                        </div>
                        <div class="col-md-12">
                            <label for="staffName" class="form-label">Content</label>
                            <textarea class="form-control" name="content" id="ckeditor">{{ old('content',$post->content) }}</textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('v1.management-memo.list') }}" class="btn btn-warning">Cancel</a>
                            <form class="row g-3"
                        action="{{ route('v1.management-memo.destroy', ['id'=>$post->id,'status'=>0]) }}"
                        method="post">
                        @csrf
                        <button type="submit" class="btn btn-danger">Delete</button>
                        @method('PUT')
                            </form>
                        </div>
                    </form>

<script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>

<script>

    ClassicEditor

        .create( document.querySelector( '#ckeditor' ),{

            ckfinder: {

                uploadUrl: '{{route('ckeditor.upload').'?_token='.csrf_token()}}',

            }

        })

        .catch( error => {



        } );

</script>
                </div>
            </div>
        </div>
    </div>
@endsection
