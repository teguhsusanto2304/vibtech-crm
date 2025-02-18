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
                @if ($item=="Vehicle Booking")
                <a href="{{ route('vehicle-booking') }}">{{ $item }}</a>
                @elseif ($item=="Vehicle Booking List")
                <a href="{{ route('vehicle-booking-list') }}">{{ $item }}</a>
                @else
                <a href="javascript:void(0);">{{ $item }}</a>
                @endif

                <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
            </li>
            @endforeach
        </ol>
    </nav>

    <h2>Vehicle Booking Detail</h2>
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
        .car-image {
      width: 400px;
      height: auto;
      margin-bottom: 20px;
    }
      </style>
    <div class="card">
        <div class="row">

            <div class="col-6">
                <img src="../assets/img/cars/volvo.png" alt="Car 1" class="car-image" id="selectedCar">
            </div>
            <div class="col-6">
                <div class="row">
                    <div class="col-6">
                        <p class="font"><strong>Vehicle:</strong></p>
                        <p>SGA 12345</p>
                    </div>
                    <div class="col-6">
                        <p class="font"><strong>Usage Timing:</strong></p>
                        <p>02:00 - 03:30</p>
                    </div>
                    <div class="col-6">
                        <p class="font"><strong>Usage Date:</strong></p>
                        <p>10/05/2024</p>
                    </div>
                    <div class="col-6">
                        <p class="font"><strong>Usage Purpose:</strong></p>
                        <p>Go to wage to collect spare parts</p>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card-footer">
                @if($id=="edit")
                    <a href="{{ route('vehicle-booking-create') }}">Edit Booking</a>
                    <a href="#">Delete Booking</a>
                  </div>
                @else
                  <p class="font">This is a pass booking</p>
                @endif
            </div>
        </div>





      </div>

    </div>
@endsection
