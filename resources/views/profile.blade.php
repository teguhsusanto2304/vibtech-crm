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
    <style>
.account-settings .user-profile {
    margin: 0 0 1rem 0;
    padding-bottom: 1rem;
    text-align: center;
}
.account-settings .user-profile .user-avatar {
    margin: 0 0 1rem 0;
}
.account-settings .user-profile .user-avatar img {
    width: 90px;
    height: 90px;
    -webkit-border-radius: 100px;
    -moz-border-radius: 100px;
    border-radius: 100px;
}
.account-settings .user-profile h5.user-name {
    margin: 0 0 0.5rem 0;
}
.account-settings .user-profile h6.user-email {
    margin: 0;
    font-size: 0.8rem;
    font-weight: 400;
    color: #9fa8b9;
}
.account-settings .about {
    margin: 2rem 0 0 0;
    text-align: center;
}
.account-settings .about h5 {
    margin: 0 0 15px 0;
    color: #007ae1;
}
.account-settings .about p {
    font-size: 0.825rem;
}

    </style>
    </div>
<div class="row gutters">
<div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
<div class="card h-100">
	<div class="card-body">
		<div class="account-settings">
			<div class="user-profile">
				<div class="user-avatar">
					<img src="{{ asset(auth()->user()->path_image) }}" alt="User Profile Picture">
				</div>
				<h5 class="user-name">{{ $user->name }}</h5>
        <h6 class="user-email"><strong>{{ $user->position }}</strong></h6>
				<h6 class="user-email">{{ $user->email }}</h6>
        <p class="mt-4"><a href="{{ route('profile.change-password')}}" class="btn btn-primary btn-sm">Change Password</a></p>
			</div>
			<div class="about">
				
			</div>
		</div>
	</div>
</div>
</div>
<div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
<div class="card h-100">
	<div class="card-body">
		<div class="row gutters">
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
				<h6 class="mb-2 text-primary">Personal Details</h6>
			</div>
			<div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
				<div class="form-group">
					<label for="fullName"><small><strong>Staff ID</strong></small></label>
					<input type="text" class="form-control-plaintext" id="fullName" disabled value="{{ $user->user_number }}">
				</div>
			</div>
      <div class="col-xl-7 col-lg-10 col-md-10 col-sm-10 col-12">
				<div class="form-group">
					<label for="eMail"><small><strong>Name</strong></small></label>
					<input type="text" class="form-control-plaintext" disabled id="eMail" value="{{ $user->name }}">
				</div>
			</div>
      <div class="col-xl-3 col-lg-10 col-md-10 col-sm-10 col-12">
				<div class="form-group">
					<label for="eMail"><small><strong>Nick Name</strong></small></label>
					<input type="email" class="form-control-plaintext" disabled id="eMail" value="{{ $user->nick_name }}">
				</div>
			</div>
			<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
				<div class="form-group">
					<label for="eMail"><small><strong>Email</strong></small></label>
					<input type="text" class="form-control-plaintext" disabled id="eMail" value="{{ $user->email }}">
				</div>
			</div>
			<div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
				<div class="form-group">
					<label for="phone"><small><strong>Phone Number</strong></small></label>
					<input type="text" class="form-control-plaintext" disabled value="{{ $user->phone_number }}">
				</div>
			</div>
			<div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
				<div class="form-group">
					<label for="eMail"><small><strong>Date of Birth</strong></small></label>
					<input type="text" class="form-control-plaintext" disabled id="eMail" value="{{ $user->dob->format('d M Y') }}">
				</div>
			</div>
		</div>
		<div class="row gutters">
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
				<h6 class="mt-3 mb-2 text-primary">Work</h6>
			</div>
      <div class="col-xl-5 col-lg-5 col-md-5 col-sm-5 col-12">
				<div class="form-group">
					<label for="phone"><small><strong>Position</strong></small></label>
					<input type="text" class="form-control-plaintext" disabled value="{{ $user->position }}">
				</div>
			</div>
			<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
				<div class="form-group">
					<label for="phone"><small><strong>Department</strong></small></label>
					<input type="text" class="form-control-plaintext" disabled value="{{ $user->department }}">
				</div>
			</div>
      <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
				<div class="form-group">
					<label for="phone"><small><strong>Joined Date</strong></small></label>
					<input type="text" class="form-control-plaintext" disabled value="{{ $user->joined_at->format('d M Y') }}">
				</div>
			</div>
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
				<div class="form-group">
					<label for="phone"><small><strong>Location</strong></small></label>
					<input type="text" class="form-control-plaintext" disabled value="{{ $user->branch_office }}">
				</div>
			</div>
		</div>
		<div class="row gutters">
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
				<div class="text-end bg-dark">
					
				</div>
			</div>
		</div>
	</div>
  <div class="card-footer text-end bg-dark">
      <img src="{{ asset('assets/img/logo.png') }}" width="170px" height="70px" class="mt-2">
  </div>
</div>
</div>
</div>

</div>
@endsection
