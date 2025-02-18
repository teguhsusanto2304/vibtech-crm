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

    <h2>Vehicle Booking</h2>
    <style>

        .button-container {
      background-color: #003366;
      color: #fff;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 10px;
      text-align: center;
    }
      </style>
    <div class="button-container">
        <a href="{{ route('vehicle-booking-create') }}" style="text-decoration: none; color: inherit;">Book A Vehicle</a>
      </div>

      <div class="button-container" style="margin-top: 10px;">
        <a href="{{ route('vehicle-booking-list') }}" style="text-decoration: none; color: inherit;">Edit Existing Booking</a>
      </div>
</div>
@endsection
