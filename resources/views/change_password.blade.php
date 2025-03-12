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
    </div>
    <div class="card mb-6">
    <h5 class="card-header">Change Password</h5>
    <div class="card-body pt-1">
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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

    <form method="POST" action="{{ route('profile.password-update') }}">
        @csrf
        <div class="row">
          <div class="mb-6 col-md-6 form-password-toggle form-control-validation">
            <label class="form-label" for="currentPassword">Current Password</label>
            <div class="input-group input-group-merge">
              <input class="form-control" type="password" name="current_password" id="current_password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
              <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="mb-6 col-md-6 form-password-toggle form-control-validation">
            <label class="form-label" for="newPassword">New Password</label>
            <div class="input-group input-group-merge">
              <input class="form-control" type="password" id="new_password" name="new_password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
              <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
            </div>
          </div>

          <div class="mb-6 col-md-6 form-password-toggle form-control-validation">
            <label class="form-label" for="confirmPassword">Confirm New Password</label>
            <div class="input-group input-group-merge">
              <input class="form-control" type="password" name="new_password_confirmation" id="new_password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
              <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
            </div>
          </div>
        </div>
        <h6 class="text-body">Password Requirements:</h6>
        <ul class="ps-4 mb-0">
          <li class="mb-4">Minimum 8 characters long - the more, the better</li>
          <li class="mb-4">At least one lowercase character</li>
          <li>At least one number, symbol, or whitespace character</li>
        </ul>
        <div class="mt-6">
          <button type="submit" class="btn btn-primary me-3">Save changes</button>
          <button type="reset" class="btn btn-label-secondary">Reset</button>
        </div>
      </form>
    </div>
  </div>
    </div>
  @endsection
