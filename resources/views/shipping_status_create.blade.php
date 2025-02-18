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
                @if ($item=="Shipping/Delivery Status")
                <a href="{{ route('shipping-status') }}">{{ $item }}</a>
                @else
                <a href="javascript:void(0);">{{ $item }}</a>
                @endif

                <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
            </li>
            @endforeach
        </ol>
    </nav>

    <h2>{{ $title }}</h2>
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



        <div class="col-md-2">
          <div class="form-group">
            <div>
                <label for="claimType" style="margin-bottom: 10px;">Item No:</label>
                <input type="text" class="form-control text-input col-md-3">
            </div>
          </div>
        </div>

        <div class="col-md-10">
            <div class="form-group">
              <div>
                  <label for="claimType" style="margin-bottom: 10px;">Item Name:</label>
                  <input type="text" class="form-control text-input col-md-3">
              </div>
            </div>
          </div>
      </div>
      <div class="row" style="margin-top: 20px">
        <div class="col-2">
            <div class="form-group">
                <label for="claimAmount" style="margin-bottom: 10px; margin-top: 10px;">Choose a tracking update:</label>
              </div>
        </div>
        <div class="col-5">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                <label class="form-check-label" for="exampleRadios1">
                  Order Placed
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2" disabled>
                <label class="form-check-label" for="exampleRadios2">
                  Shipment Scheduled
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios3" value="option3" disabled>
                <label class="form-check-label" for="exampleRadios3">
                    Shipment Confirmed
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios3" value="option3" disabled>
                <label class="form-check-label" for="exampleRadios3">
                    Warehouse Received
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios3" value="option3" disabled>
                <label class="form-check-label" for="exampleRadios3">
                    Out for delivery
                </label>
              </div>
        </div>
      </div>

      <div class="col-md-12" style="margin-top: 20px">
        <div class="form-group">
          <div>
              <label for="claimType" style="margin-bottom: 10px;">Remarks:</label>
              <textarea class="form-control area"  rows="3"></textarea>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
            <div class="card_upload">
                <div class="upload-area">
                <i class="fas fa-upload upload-icon"></i>
                <label for="claimAmount" style="margin-bottom: 10px; margin-top: 10px;">Upload item photo</label>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card_upload">
                <div class="upload-area">
                <i class="fas fa-upload upload-icon"></i>
                <label for="claimAmount" style="margin-bottom: 10px; margin-top: 10px;">Upload PO</label>
                </div>
            </div>
        </div>
      </div>




<br>
    </div>
    </div>
      <button type="button" class="btn btn-primary">Create New Order</button>
      <a class="btn btn-warning" href="{{ route('shipping-status') }}">Back</a>

    </div>
@endsection
