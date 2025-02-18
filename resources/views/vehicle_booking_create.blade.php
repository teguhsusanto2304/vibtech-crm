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
                @else
                <a href="javascript:void(0);">{{ $item }}</a>
                @endif

                <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
            </li>
            @endforeach
        </ol>
    </nav>

    <h2>Vehicle Booking Form</h2>
    <style>
        /* ... other styles ... */

        input[type="time"],
        input[type="radio"],
        input[type="date"] {
          background-color: white; /* Set background color to white */
          border: 1px solid #ccc; /* Add a light gray border */
          border-radius: 5px;
          padding: 10px;
        }
        .text-input {
          background-color: white; /* Set background color to white */
          border: 1px solid #ccc; /* Add a light gray border */
          border-radius: 5px;
          padding: 10px;
        }
        .area {
            background-color: white; border: 1px solid #ccc; border-radius: 5px; padding: 10px;
        }


    .card_upload {
      background-color: #243B65; /* Slightly lighter blue */
      padding: 20px;
      border-radius: 5px;
      margin-bottom: 20px;
      text-align: center;
    }

    .upload-area {
      border: 2px dashed #fff;
      border-radius: 5px;
      padding: 20px;
      cursor: pointer;
    }

    .upload-icon {
      font-size: 40px;
      color: #fff; /* White icon color */
      margin-bottom: 10px;
    }
    .card_main {
      background-color: #003366; /* Dark blue */
      border-radius: 10px 10px 0 0; /* Top corners rounded */
      color: #fff;
      padding-top: 20px;
    }
    .form-check-label {
        color: #fff;
    }
    .input-group-text {
        color: #fff;
    }
    .input-group-text:focus {
  background-color: #fff;
  color: #003366; /* Invert colors when focused */
}

.form-control:focus {
  background-color: #fff; /* Match input field background on focus */
}
.car-image {
      width: 400px;
      height: auto;
      margin-bottom: 20px;
    }

    .radio-group {
      margin-top: 20px;
    }
  </style>
    <div class="container card_main" style="margin-bottom: 20px;">
    <div class="row">


        <label for="claimType" style="margin-bottom: 10px;">Choose Vehice:</label>
        <div class="col-12">
            <div class="row">
                <div class="col-6">
                <img src="../assets/img/cars/volvo.png" alt="Car 1" class="car-image" id="selectedCar">
                </div>
                <div class="col-6">
                <div class="radio-group">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="car" id="car1" value="car1" checked>
                    <label class="form-check-label" for="car1">
                      <span class="car-code">SGA12345</span>
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="car" id="car2" value="car2">
                    <label class="form-check-label" for="car2">
                      <span class="car-code">SGB15234</span>
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="car" id="car3" value="car3">
                    <label class="form-check-label" for="car3">
                      <span class="car-code">SHA10011</span>
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="car" id="car4" value="car4">
                    <label class="form-check-label" for="car4">
                      <span class="car-code">SLA10345</span>
                    </label>
                  </div>
                </div>
              </div>

              <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      $('input[type="radio"]').change(function() {
        var carId = $(this).val();
        var carImage = $('#selectedCar');

        switch (carId) {
          case 'car2':
            carImage.attr('src', '../assets/img/cars/mercy.png'); // Replace with actual image paths
            break;
          case 'car3':
            carImage.attr('src', '../assets/img/cars/bmw.png');
            break;
          case 'car4':
            carImage.attr('src', '../assets/img/cars/ford.png');
            break;
          default:
            carImage.attr('src', '../assets/img/cars/volvo.png');
        }
      });
    });
  </script>


      <div class="form-group">
        <label for="dateOfOccurrence" style="margin-bottom: 10px;">Choose a date:</label>
          <input type="date" class="form-control" id="startDate">
        </div>
      </div>

      <div class="form-group">
        <label for="dateOfOccurrence" style="margin-bottom: 10px;">Choose a Time:</label>
        <div class="input-group">
          <input type="time" class="form-control" id="startDate">
          <span class="input-group-text">to</span>
          <input type="time" class="form-control" id="endDate">
        </div>
      </div>
      <div class="form-group" style="margin-top: 20px; margin-bottom: 20px;;">
        <textarea class="form-control area"  rows="3" placeholder="State your Vehice usage purpose"></textarea>
      </div>
    </div>
    </div>
    </div>
    </div>
      <button type="button" class="btn btn-primary">Create Booking</button>
      <button type="button" class="btn btn-secondary">Reset All</button>

    </div>
@endsection
