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
                @else
                <a href="javascript:void(0);">{{ $item }}</a>
                @endif

                <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
            </li>
            @endforeach
        </ol>
    </nav>

    <h2>Submit Claim Form</h2>
    <style>
        /* ... other styles ... */

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
  </style>
    <div class="container card_main" style="margin-bottom: 20px;">
    <div class="row">


        <label for="claimType" style="margin-bottom: 10px;">Claim Type:</label>
        <div class="col-md-6">
          <div class="form-group">
            <div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="claimType" id="localTravel">
                <label class="form-check-label" for="localTravel">Local Travel Expenses</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="claimType" id="overseasTravel">
                <label class="form-check-label" for="overseasTravel">Overseas Travel Expenses</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="claimType" id="accommodation">
                <label class="form-check-label" for="accommodation">Accommodation</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="claimType" id="meals">
                <label class="form-check-label" for="meals">Meals & Entertainment</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="claimType" id="taxi">
                <label class="form-check-label" for="taxi">Taxi & Ride-Hailing Expenses</label>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="claimType" id="fuel">
              <label class="form-check-label" for="fuel">Fuel & Parking Expenses</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="claimType" id="officeSupplies">
              <label class="form-check-label" for="officeSupplies">Office Supplies</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="claimType" id="itEquipment">
              <label class="form-check-label" for="itEquipment">IT Equipment & Accessories</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="claimType" id="training">
              <label class="form-check-label" for="training">Training & Development</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="claimType" id="miscellaneous">
              <label class="form-check-label" for="miscellaneous">Miscellaneous/Other</label>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="dateOfOccurrence" style="margin-bottom: 10px;">Date of Occurrence:</label>
        <div class="input-group">
          <input type="date" class="form-control" id="startDate">
          <span class="input-group-text">to</span>
          <input type="date" class="form-control" id="endDate">
        </div>
      </div>
      <div class="row">
        <div class="col-2">
            <div class="form-group">
                <label for="claimAmount" style="margin-bottom: 10px; margin-top: 10px;">Claim Currency:</label>
                <select class="form-control text-input" id="claimAmount" >
                    <option>SGD</option>
                    <option>IDR</option>
                    <option>MYR</option>
                </select>
              </div>
        </div>
        <div class="col-5">
            <div class="form-group">
                <label for="claimAmount" style="margin-bottom: 10px; margin-top: 10px;">Claim Amount:</label>
                <input type="text" class="form-control text-input" id="claimAmount" placeholder="Enter Claim Amount">
              </div>
        </div>
      </div>




      <label for="claimAmount" style="margin-bottom: 10px; margin-top: 10px;">Attach Documents</label>
      <div class="card_upload">
        <div class="upload-area">
          <i class="fas fa-upload upload-icon"></i>
          <p>Upload Document Here (Up to 10 files/50 MB Max)</p>
          <p>Drag and drop files here</p>
        </div>
      </div>
      <label for="claimAmount" style="margin-bottom: 10px; margin-top: 10px;">Remarks</label>
        <textarea class="form-control area"  rows="3"></textarea>



<br>
    </div>
    </div>
      <button type="button" class="btn btn-primary">Create Claim</button>
      <button type="button" class="btn btn-secondary">Reset All</button>

    </div>
@endsection
