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
    <div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('v1.configuration.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="tab-content" id="settingsTabContent">
                @foreach($groupedConfig as $category => $settings)
                    <div class="tab-pane fade @if($loop->first) show active @endif"
                         id="{{ $category }}" role="tabpanel">
                        @foreach($settings as $key => $config)
                            <div class="mb-3">
                                <label class="form-label text-capitalize">{{ str_replace('_', ' ', $key) }}</label>
                                @if($config['type'] === 'file')
                                    <input type="file" name="{{ $key }}" class="form-control">
                                    @if($config['value'])
                                        <small class="form-text text-muted">Current file: {{ $config['value'] }}</small>
                                        <img src="{{ asset($config['value']) }}" alt="Current Image" class="mt-2" style="max-width: 150px;">
                                    @endif
                                @else
                                    <input type="text" name="{{ $key }}" class="form-control" value="{{ $config['value'] }}">
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
</div>


@endsection
