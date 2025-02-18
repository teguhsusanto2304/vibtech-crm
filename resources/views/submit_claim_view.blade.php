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
                @if ($item=="Submit Claim")
                <a href="{{ route('submit-claim') }}">{{ $item }}</a>
                @elseif ($item=="Submit Claim Status")
                <a href="{{ route('submit-claim-list') }}">{{ $item }}</a>
                @else
                <a href="javascript:void(0);">{{ $item }}</a>
                @endif

                <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
            </li>
            @endforeach
        </ol>
    </nav>

    <h2>Submit Claim Detail</h2>
    <style>


        .card {
          background-color: #243B65; /* Slightly lighter blue */
          padding: 30px;
          border-radius: 10px;
          width: 90%; /* Adjust width as needed */
          margin: 40px;;
          text-align: center;
        }

        .card-header {
          display: flex;
          justify-content: space-between;
          margin-bottom: 20px;
        }

        .card-header h2 {
          margin: 0;
        }

        .card-header .badge {
          background-color: #ffc107; /* Yellow for Pending status */
          color: #000;
          padding: 5px 10px;
          border-radius: 5px;
        }

        .card-body {
          display: flex;
          flex-wrap: wrap;
        }

        .card-body > div {
          flex: 0 0 50%; /* Equal width columns */
          margin-right: 20px;
        }

        .card-body > div:last-child {
          margin-right: 0;
        }

        .card-footer {
          text-align: center;
          margin-top: 20px;
        }

        .card-footer a {
          color: #fff;
          text-decoration: none;
          margin: 0 10px;
        }

        .employee-image {
          width: 100px;
          height: 100px;
          border-radius: 50%;
          overflow: hidden;
          margin-right: 10px;
        }
        .font {
            color: #fff;
        }
      </style>
    <div class="card">
        <div class="row">
            <div class="col-10">
              <h2 class="font">Claim</h2>
              <p><label class="font">Claim ID: 12345678</label></p>
            <p><label class="font">Claim Created: 01/05/2024</label></p>
            <img src="/assets/img/avatars/1.png" alt="Employee Image" class="employee-image" width="200px" style="margin-bottom: 30px;">
            </div>
            <div class="col-2" >
                @if($id=="pending")
                <span class="badge bg-warning">Pending</span>
                @elseif ($id=="approved")
                <span class="badge bg-success">Approved</span>
                @else
                <span class="badge bg-danger">Rejected</span>
                @endif
            </div>
            <div class="col-5">
                <p class="font"><strong>Staff Name:</strong></p>
                <p>Jhon Doe</p>
            </div>
            <div class="col-5">
                <p class="font"><strong>Claim Type:</strong></p>
                <p>Overseas Travel Expenses</p>
            </div>
            <div class="col-2" >
            </div>
            <div class="col-5">
                <p class="font"><strong>Staff ID:</strong></p>
                <p>S095</p>
            </div>
            <div class="col-5">
                <p class="font"><strong>Date:</strong></p>
                <p>16/01/2025</p>
            <p>17/01/2025</p>
            <p>18/01/2025</p>
            </div>
            <div class="col-2" >
            </div>
            <div class="col-5">
                <p class="font"><strong>Remarks:</strong></p>
                <p>For 2025 exhibition meals</p>
            </div>
            <div class="col-7" >
            </div>
            <div class="col-5">
                <p class="font"><strong>Additional Documents:</strong></p>
                <p>FastFoodReceipt.jpg</p>
            </div>
            <div class="col-5">
                <p class="font"><strong>Claim Amount:</strong></p>
                <p>$50.00</p>
            </div>
            <div class="col-2" >
            </div>
            <div class="col-10">
                <div class="card-footer">
                    <a href="#">Print Claim</a>
                    <a href="#">Download Claim (PDF)</a>
                  </div>
            </div>

            <div class="col-2" >
            </div>
        </div>





      </div>

    </div>
@endsection
