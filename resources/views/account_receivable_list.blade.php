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
        <div class="col-sm-4" style="text-align: right">
            <button class="btn" data-bs-toggle="modal" data-bs-target="#newEntryModal" style="background-color: #243B65;color:#fff;">New Entry</button>
          </div>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="newEntryModal" tabindex="-1" aria-labelledby="updateModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="updateModalLabel">New Entry</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                      aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div class="col-6">
                          <label>Company</label>
                      </div>
                      <div class="col-6">
                              <input type="text" class="form-control" placeholder="company name">
                      </div>
                      <div class="col-6" style="margin-top: 20px;"><label>Invoice Number</label></div>
                      <div class="col-6" style="margin-top: 20px;">
                            <input type="text" class="form-control" placeholder="Invoice Number">
                      </div>

                      <div class="col-6" style="margin-top: 20px;"><label>Invoice Date</label></div>
                      <div class="col-6" style="margin-top: 20px;"><label>Due Date</label></div>
                      <div class="col-6" style="margin-top: 20px;">
                            <input type="date" class="form-control" placeholder="Invoice Number">
                      </div>
                      <div class="col-6" style="margin-top: 20px;">
                        <input type="date" class="form-control" placeholder="Invoice Number">
                        </div>
                      <div class="col-6" style="margin-top: 20px;"><label>payment status</label></div>
                      <div class="col-6" style="margin-top: 20px;">
                          <select class="form-control">
                            <option value="">Choose Payment status</option>
                            <option value="">Paid</option>
                            <option value="">Unpaid</option>
                            <option value="">Overdue</option>
                          </select>
                      </div>
                  </div>


              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-primary">Enter</button>
              </div>
          </div>
      </div>
  </div>
      <br>

      <table class="table table-hover">
        <thead >
          <tr>
            <th class="font"><label class="font">Company</label></th>
            <th><label class="font">Invoice No.</label></th>
            <th><label class="font">Invoice Date</label> <i class="fas fa-sort-down sort-icon"></i></th>
            <th><label class="font">Invoice Due Date</label> <i class="fas fa-sort-down sort-icon"></i></th>
            <th><label class="font">Payment Statuse</label> <i class="fas fa-sort-down sort-icon"></i></th>
            <th><label class="font">Action</label> <i class="fas fa-sort-down sort-icon"></i></th>
          </tr>
        </thead>
        <tbody>
            <tr>
                <td><label class="font">Senoko Energy</label></td>
                <td><label class="font">Vib12345</label></td>
                <td><label class="font">15/05/2025</label></td>
                <td><label class="font">25/05/2025</label></td>
                <td ><span class="badge bg-warning">Overdue</span></td>
                <td><a href="#" class="view-history" data-bs-toggle="modal" data-bs-target="#historyModal" data-history="History for Vib12345">View History</a></td>
            </tr>
            <tr>
                <td><label class="font">PUB</label></td>
                <td><label class="font">Vib54321</label></td>
                <td><label class="font">19/05/2025</label></td>
                <td><label class="font">05/06/2025</label></td>
                <td ><span class="badge bg-danger">Unpaid</span></td>
                <td><a href="#" class="view-history" data-bs-toggle="modal" data-bs-target="#historyModal" data-history="History for Vib12345">View History</a></td>
            </tr>
            <tr>
                <td><label class="font">KWRP</label></td>
                <td><label class="font">Vib10001</label></td>
                <td><label class="font">20/05/2025</label></td>
                <td><label class="font">25/06/2025</label></td>
                <td ><span class="badge bg-success">Paid</span></td>
                <td><a href="#" class="view-history" data-bs-toggle="modal" data-bs-target="#historyModal" data-history="History for Vib12345">View History</a></td>
            </tr>
        </tbody>
      </table>
      </div>
      <!-- Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">History Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <div class="details">
                            <span>Company:</span> KWRP
                        </div>
                        <div class="details">
                            <span>Invoice No:</span> Vib10001
                        </div>
                        <div class="details">
                            <span>Invoice Date:</span> 20/05/2025
                        </div>
                        <div class="details">
                            <span>Invoice Due Date:</span> 25/06/2025
                        </div>
                        <div class="details">
                            <span>Payment Status:</span> <span class="badge bg-success">Paid</span>
                        </div>

                    </div>
                    <div class="col-6">
                        <label>Payment Status</label>
                        <div class="card" style="background-color: #ede9e9;padding:10px;">
                            <div class="history-item">
                                <span class="date">23/06/2025</span><br>
                                <span class="event">System: Beiling Teo changed this status to Paid</span>
                            </div>
                            <hr>
                            <div class="history-item">
                                <span class="date">20/05/2025</span><br>
                                <span class="event">System: Beiling Teo created this status as Unpaid</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


</div>
@endsection
