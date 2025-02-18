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
    <h2>Management Memo</h2>
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
              <option>Claim ID</option>
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
        <thead>
          <tr>
            <th><label class="font">Memo Title</label></th>
            <th><label class="font">Issued By</label></th>
            <th><label class="font">Date</label></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><label class="font">Do not use ground floor meeting room</label></td>
            <td><label class="font">Helen</label></td>
            <td><label class="font">25/01/2024</label></td>
            <td><a href="#" class="btn btn-sm btn-primary">Read Memo</a></td>
          </tr>
          <tr>
            <td><label class="font">Do not use ground floor meeting room</label></td>
            <td><label class="font">Helen</label></td>
            <td><label class="font">25/01/2024</label></td>
            <td><a href="#" class="btn btn-sm btn-primary">Read Memo</a></td>
          </tr>
          <tr>
            <td><label class="font">Do not use ground floor meeting room</label></td>
            <td><label class="font">Helen</label></td>
            <td><label class="font">25/01/2024</label></td>
            <td><a href="#" class="btn btn-sm btn-primary">Read Memo</a></td>
          </tr>
          <tr>
            <td><label class="font">Do not use ground floor meeting room</label></td>
            <td><label class="font">Helen</label></td>
            <td><label class="font">25/01/2024</label></td>
            <td><a href="#" class="btn btn-sm btn-primary">Read Memo</a></td>
          </tr>
          <tr>
            <td><label class="font">Do not use ground floor meeting room</label></td>
            <td><label class="font">Helen</label></td>
            <td><label class="font">25/01/2024</label></td>
            <td><a href="#" class="btn btn-sm btn-primary">Read Memo</a></td>
          </tr>
          <tr>
            <td><label class="font">Do not use ground floor meeting room</label></td>
            <td><label class="font">Helen</label></td>
            <td><label class="font">25/01/2024</label></td>
            <td><a href="#" class="btn btn-sm btn-primary">Read Memo</a></td>
          </tr>
        </tbody>
      </table>
</div>
@endsection
