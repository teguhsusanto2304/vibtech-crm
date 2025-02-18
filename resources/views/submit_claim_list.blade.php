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

    <h2>Submit Claim Status</h2>
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
      </div>
      <br>

      <table class="table table-hover">
        <thead >
          <tr>
            <th class="font"><label class="font">Claim ID</label></th>
            <th><label class="font">Claim Created</label></th>
            <th><label class="font">Claim Status</label> <i class="fas fa-sort-down sort-icon"></i></th>
            <th><label class="font">Action</label></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><label class="font">12345678</label></td>
            <td><label class="font">01/05/2024</label></td>
            <td><span class="badge bg-warning">Pending</span></td>
            <td><a href="{{ route('submit-claim-view',['id'=>'pending'])}}" class="btn btn-sm btn-primary">View Claim</a></td>
          </tr>
          <tr>
            <td><label class="font">87654321</label></td>
            <td><label class="font">25/04/2024</label></td>
            <td><span class="badge bg-success">Approved</span></td>
            <td><a href="{{ route('submit-claim-view',['id'=>'approved'])}}" class="btn btn-sm btn-primary">View Claim</a></td>
          </tr>
          <tr>
            <td><label class="font">55116612</label></td>
            <td><label class="font">05/03/2024</label></td>
            <td><span class="badge bg-danger">Rejected</span></td>
            <td><a href="{{ route('submit-claim-view',['id'=>'rejected'])}}" class="btn btn-sm btn-primary">View Claim</a></td>
          </tr>
        </tbody>
      </table>
      </div>
      <script src="https://kit.fontawesome.com/your-fontawesome-key.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</div>
@endsection
