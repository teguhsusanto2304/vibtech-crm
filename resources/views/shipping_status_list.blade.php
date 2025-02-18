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


        .container {
          padding: 20px;
        }

        table {
          width: 100%;
          border-collapse: collapse;
          background: #243B65;
          color: #fff;
          -bs-table-striped-bg:#626b7d;
        }


        th, td {
      border: 1px solid #fff; /* White border */
      padding: 10px;
      text-align: left;
      color: #fff;
    }

        th {
          background-color: #243B65; /* Slightly lighter blue for header */
        }

        .sort-icon {
          margin-left: 5px;
          color: #ffd700; /* Gold color */
        }
        .font {
            color: #fff;
        }
      </style>
      <div class="container">
      <div class="row">
        <div class="col-sm-4">
          <select class="form-control">
            <option>Sort By</option>
            <option>Claim Created</option>
            <option>Claim Status</option>
          </select>
        </div>
        <div class="col-sm-4">
          <select class="form-control">
            <option>Filter By Year</option>
            <option>2023</option>
            <option>2024</option>
          </select>
        </div>
        <div class="col-sm-4">
            <input type="text" class="form-control" placeholder="search">
          </div>
      </div>
      <br>

      <table class="table table-hover">
        <thead >
          <tr>
            <th class="font"><label class="font">Item Name</label></th>
            <th><label class="font">Item No.</label></th>
            <th><label class="font">PO</label> <i class="fas fa-sort-down sort-icon"></i></th>
            <th><label class="font">Created On</label> <i class="fas fa-sort-down sort-icon"></i></th>
            <th><label class="font">Order Person</label> <i class="fas fa-sort-down sort-icon"></i></th>
            <th><label class="font">&nbsp;</label></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><label class="font">PC Monitor 30 Inch</label></td>
            <td><label class="font">VTG876543456</label></td>
            <td><label class="font">MON4567.pdf</label></td>
            <td><label class="font">01/05/2024</label></td>
            <td><label class="font">Beiling Teo</label></td>
            <td><a href="{{ route('shipping-status-view',['id'=>'pending'])}}" class="btn btn-sm btn-primary">Update Order</a></td>
          </tr>
          <tr>
            <td><label class="font">UPS 5000 Watt</label></td>
            <td><label class="font">VTG8765433456</label></td>
            <td><label class="font">UPS4567.pdf</label></td>
            <td><label class="font">01/05/2024</label></td>
            <td><label class="font">Beiling Teo</label></td>
            <td><a href="{{ route('shipping-status-view',['id'=>'pending'])}}" class="btn btn-sm btn-primary">Update Order</a></td>
          </tr>
          <tr>
            <td><label class="font">Epson Dotmatix Printer LX-300 </label></td>
            <td><label class="font">VTG8764444</label></td>
            <td><label class="font">PRT4567.pdf</label></td>
            <td><label class="font">01/05/2024</label></td>
            <td><label class="font">Beiling Teo</label></td>
            <td><a href="{{ route('shipping-status-view',['id'=>'pending'])}}" class="btn btn-sm btn-primary">Update Order</a></td>
          </tr>
        </tbody>
      </table>
      </div>
      <script src="https://kit.fontawesome.com/your-fontawesome-key.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</div>
@endsection
