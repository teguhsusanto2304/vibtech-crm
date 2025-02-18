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
.profile-card {
  display: flex;
  border: 1px solid #ccc;
  border-radius: 5px;
  padding: 20px;
  background-color: #172636;
}

.profile-image {
  margin-right: 20px;
}

.profile-image img {
  width: 150px;
  height: 150px;
  border-radius: 10%;
}
.profile-info {
    width: 100%;
}

.profile-info table {
  border-collapse: collapse;
  color: white;
  width: 100%;
}

.profile-info td {
  padding: 5px;
  width: 25%;
}

.actions {
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
}

.actions button {
  padding: 10px 20px;
  border: none;
  border-radius: 3px;
  background-color: #007bff; /* Blue button color */
  color: white;
  cursor: pointer;
}
.vertical-line {
  border-left: 2px solid #ccc; /* Adjust width and color as needed */
  height: 100px; /* Adjust height as needed */
}

.button-container {
  text-align: center; /* Center the buttons horizontally */
  margin-top: 50px; /* Adjust margin as needed */
}

.button {

  text-decoration: none; /* Remove default underline */
  color: #fff; /* Text color */

}

.button:hover {
  color: #006680; /* Slightly lighter color on hover */
}
    </style>
    <div class="profile-card">
        <div class="profile-image">
            <img src="{{ asset('assets/img/photos/'.auth()->user()->path_image) }}" alt="User Profile Picture">
          <div class="button-container">
            <a href="#" class="button">Generate QR Code</a>
            <br>
            <br>
            <a href="#" class="button">Reset Password</a>
          </div>
        </div>
        <div class="profile-info">
          <table width="100%">
            <tr>
              <td width="25%">Staff Name:</td>
              <td width="25%">{{ $user->name }}</td>
              <td width="25%">Date of Birth:</td>
              <td width="25%">01/06/1995</td>
            </tr>
            <tr>
              <td>Staff ID:</td>
              <td>S095</td>
              <td>Contact Number:</td>
              <td>+65 91234567</td>
            </tr>
            <tr>
              <td>Department:</td>
              <td>{{ $user->department }}</td>
              <td>Email:</td>
              <td>{{ $user->email }}</td>
            </tr>
            <tr>
              <td>Location:</td>
              <td>{{ $user->branch_office }}</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>Position:</td>
              <td>{{ $user->position }}</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>Date Joined:</td>
              <td>20/03/2022</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td> <img src="../assets/img/logo.png" width="170px" height="70px"></td>
            </tr>
          </table>
        </div>
      </div>
    </div>

</div>
@endsection
