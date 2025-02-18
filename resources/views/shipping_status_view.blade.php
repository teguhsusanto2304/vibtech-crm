@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- custom-icon Breadcrumb-->
            <nav aria-label="breadcrumb">

                <ol class="breadcrumb breadcrumb-custom-icon">
                    @foreach ($breadcrumb as $item)
                        <li class="breadcrumb-item">
                            @if ($item == "Shipping/Delivery Status")
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
                    background-color: white;
                    /* Set background color to white */
                    border: 1px solid #ccc;
                    /* Add a light gray border */
                    border-radius: 5px;
                    padding: 10px;
                }

                .text-input {
                    background-color: white;
                    /* Set background color to white */
                    border: 1px solid #ccc;
                    /* Add a light gray border */
                    border-radius: 5px;
                    padding: 10px;
                }

                .area {
                    background-color: white;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                    padding: 10px;
                }


                .card_upload {
                    background-color: #243B65;
                    /* Slightly lighter blue */
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
                    color: #fff;
                    /* White icon color */
                    margin-bottom: 10px;
                }

                .card_main {
                    background-color: #003366;
                    /* Dark blue */
                    border-radius: 10px 10px 0 0;
                    /* Top corners rounded */
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
                    color: #003366;
                    /* Invert colors when focused */
                }

                .form-control:focus {
                    background-color: #fff;
                    /* Match input field background on focus */
                }
            </style>
            <div class="card_main" style="background-color: #172636">
                <div class="row" style="margin: 20px;">
                    <div class="col-6">
                        <div class="form-group">
                            <div>
                                <label for="claimType" style="margin-bottom: 10px;"><strong>Item Name:</strong></label>
                                <p><label> ASUS Monitor 23 Inchi </label></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                <label for="claimType" style="margin-bottom: 10px;"><strong>Item No:</strong></label>
                                <p><label> VTG123456 </label></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                <label for="claimType" style="margin-bottom: 10px;"><strong>Purchased Order
                                        (PO):</strong></label>
                                <p><label> VTG123456.pdf </label></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                <label for="claimType" style="margin-bottom: 10px;"><strong>Create On:</strong></label>
                                <p><label> 14/02/2025 </label></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                <label for="claimType" style="margin-bottom: 10px;"><strong>Oder Person:</strong></label>
                                <p><label> Beiling Teo </label></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                <label for="claimType" style="margin-bottom: 10px;"><strong>Delivery Order:</strong></label>
                                <p><label> Upload </label></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group" style="text-align: right;">
                            <div>
                                <label for="claimType" style="margin-bottom: 10px;"><strong>Status:</strong></label>
                                @if(request()->get('id')=="pending")
                                    <p><span class="badge bg-warning"> Inactived </span></p>
                                @else
                                <p><span class="badge bg-success"> Completed </span></p>
                                @endif
                            </div>
                        </div>
                        <div class="form-group" style="text-align: center;">
                            <img src="../assets/img/products/monitor.png" width="350px" height="300px">
                        </div>
                    </div>
                </div>
                <hr style="background-color: #fff">
                <div class="row" style="margin: 12px">
                    <div class="col-9">
                        <h2 style="color: #fff">Tracking Update</h2>
                    </div>
                    <div class="col-3" style="text-align: right">
                        @if(request()->get('id')!="pending")
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal">Add
                            Update</button>
                        @endif
                    </div>
                </div>
                <div class="card" style="background-color: #004080;margin: 20px;">
                    <style>
                        .py-8 {
                            padding-bottom: 4.5rem !important;
                            padding-top: 4.5rem !important
                        }

                        @media(min-width:576px) {
                            .py-sm-8 {
                                padding-bottom: 4.5rem !important;
                                padding-top: 4.5rem !important
                            }
                        }

                        @media(min-width:768px) {
                            .py-md-8 {
                                padding-bottom: 4.5rem !important;
                                padding-top: 4.5rem !important
                            }
                        }

                        @media(min-width:992px) {
                            .py-lg-8 {
                                padding-bottom: 4.5rem !important;
                                padding-top: 4.5rem !important
                            }
                        }

                        @media(min-width:1200px) {
                            .py-xl-8 {
                                padding-bottom: 4.5rem !important;
                                padding-top: 4.5rem !important
                            }
                        }

                        @media(min-width:1400px) {
                            .py-xxl-8 {
                                padding-bottom: 4.5rem !important;
                                padding-top: 4.5rem !important
                            }
                        }

                        .bsb-timeline-4 {
                            --bsb-tl-color: var(--bs-primary-bg-subtle);
                            --bsb-tl-circle-color: var(--bs-light);
                            --bsb-tl-circle-border-color: var(--bs-primary);
                            --bsb-tl-circle-size: 16px;
                            --bsb-tl-circle-border-size: 2px;
                            --bsb-tl-circle-offset: 8px;
                            --bsb-tl-indicator-color: var(--bs-white)
                        }

                        .bsb-timeline-4 .timeline {
                            list-style: none;
                            margin: 0;
                            padding: 0;
                            position: relative
                        }

                        .bsb-timeline-4 .timeline:after {
                            background-color: var(--bsb-tl-color);
                            bottom: 0;
                            content: "";
                            left: 0;
                            margin-left: -1px;
                            position: absolute;
                            top: 0;
                            width: 2px
                        }

                        @media(min-width:768px) {
                            .bsb-timeline-4 .timeline:after {
                                left: 50%
                            }
                        }

                        .bsb-timeline-4 .timeline>.timeline-item {
                            margin: 0;
                            padding: 0;
                            position: relative
                        }

                        .bsb-timeline-4 .timeline>.timeline-item:after {
                            background: var(--bsb-tl-circle-color);
                            border: var(--bsb-tl-circle-border-size) solid var(--bsb-tl-circle-border-color);
                            border-radius: 50%;
                            content: "";
                            height: var(--bsb-tl-circle-size);
                            left: calc(var(--bsb-tl-circle-offset)*-1);
                            position: absolute;
                            top: calc(50% - var(--bsb-tl-circle-offset));
                            width: var(--bsb-tl-circle-size);
                            z-index: 1
                        }

                        .bsb-timeline-4 .timeline>.timeline-item .timeline-body {
                            margin: 0;
                            padding: 0;
                            position: relative
                        }

                        .bsb-timeline-4 .timeline>.timeline-item .timeline-meta {
                            padding: 2.5rem 0 1rem 2.5rem
                        }

                        .bsb-timeline-4 .timeline>.timeline-item .timeline-content {
                            padding: 0 0 2.5rem 2.5rem;
                            position: relative
                        }

                        @media(min-width:768px) {
                            .bsb-timeline-4 .timeline>.timeline-item {
                                width: 50%
                            }

                            .bsb-timeline-4 .timeline>.timeline-item .timeline-meta {
                                margin-bottom: 0;
                                position: absolute;
                                top: calc(50% - 17px);
                                width: 100%;
                                z-index: 1
                            }

                            .bsb-timeline-4 .timeline>.timeline-item .timeline-content {
                                padding: 2.5rem
                            }

                            .bsb-timeline-4 .timeline>.timeline-item .timeline-indicator {
                                position: relative
                            }

                            .bsb-timeline-4 .timeline>.timeline-item .timeline-indicator:after {
                                border-width: 1px;
                                border: 10px solid var(--bsb-tl-indicator-color);
                                border-color: transparent var(--bsb-tl-indicator-color) transparent transparent;
                                border-left-width: 0;
                                content: "";
                                left: calc(2.5rem - 10px);
                                position: absolute;
                                top: calc(50% - var(--bsb-tl-circle-offset));
                                z-index: 2
                            }

                            .bsb-timeline-4 .timeline>.timeline-item.left {
                                left: 0
                            }

                            .bsb-timeline-4 .timeline>.timeline-item.left:after {
                                left: auto;
                                right: calc(var(--bsb-tl-circle-offset)*-1)
                            }

                            .bsb-timeline-4 .timeline>.timeline-item.left .timeline-meta {
                                padding: 0 0 0 2.5rem;
                                right: -100%
                            }

                            .bsb-timeline-4 .timeline>.timeline-item.left .timeline-indicator:after {
                                border-width: 1px;
                                border: 10px solid var(--bsb-tl-indicator-color);
                                border-color: transparent transparent transparent var(--bsb-tl-indicator-color);
                                border-right-width: 0;
                                left: auto;
                                right: calc(2.5rem - 10px)
                            }

                            .bsb-timeline-4 .timeline>.timeline-item.right {
                                left: 50%
                            }

                            .bsb-timeline-4 .timeline>.timeline-item.right .timeline-meta {
                                display: flex;
                                justify-content: flex-end;
                                left: -100%;
                                padding: 0 2.5rem 0 0
                            }

                            .text-light-title {
                                color: #fff;
                                font-size: large;
                                font-style: bold;
                            }
                        }
                    </style>

                    <section class="bsb-timeline-4" style="background-color: #004080">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-md-10">
                                    <ul class="timeline">
                                        <li class="timeline-item left">
                                            <div class="timeline-body">
                                                <div class="timeline-meta">
                                                    <div class="row">
                                                        <p class="text-light">15/05/2025<br>
                                                            20:05 PM<br>
                                                            Beiling Teo</p>
                                                    </div>
                                                </div>
                                                <div class="timeline-content">
                                                    <div class="row">
                                                        <div class="p-xl-2">
                                                            <p class="text-light-title">
                                                                Order Placed
                                                            </p>
                                                            <p>
                                                                Order has been placed with suplier
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="timeline-item right">
                                            <div class="timeline-body">
                                                <div class="timeline-meta">
                                                    <div class="row">
                                                        <p class="text-light">20/05/2025<br>
                                                            01:45 PM<br>
                                                            Beiling Teo</p>
                                                    </div>
                                                </div>
                                                <div class="timeline-content">
                                                    <div class="row">
                                                        <div class="p-xl-2">
                                                            <p class="text-light-title">
                                                                Shipment Scheduled
                                                            </p>
                                                            <p>
                                                                Just been informend shipment scheduled for 11/05/2025
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @if(request()->get('id')=="pending")
                                        <li class="timeline-item left">
                                            <div class="timeline-body">
                                                <div class="timeline-meta">
                                                    <div class="row">
                                                        <p class="text-light">21/05/2025<br>
                                                            01:45 PM<br>
                                                            Beiling Teo</p>
                                                    </div>
                                                </div>
                                                <div class="timeline-content">
                                                    <div class="row">
                                                        <div class="p-xl-2">
                                                            <p class="text-light-title">
                                                                <label style="color: crimson">Order Cancelled</label>
                                                            </p>
                                                            <p>
                                                                the order is cancelled as supplier informed no more stock left
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @else
                                        <li class="timeline-item left">
                                            <div class="timeline-body">
                                                <div class="timeline-meta">

                                                </div>
                                                <div class="timeline-content">
                                                    <div class="row">
                                                        <div class="p-xl-2">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @endif

                                        <li class="timeline-item right">
                                            <div class="timeline-body">
                                                <div class="timeline-meta">

                                                </div>
                                                <div class="timeline-content">
                                                    <div class="row">
                                                        <div class="p-xl-2">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="timeline-item left">
                                            <div class="timeline-body">
                                                <div class="timeline-meta">

                                                </div>
                                                <div class="timeline-content">
                                                    <div class="row">
                                                        <div class="p-xl-2">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Modal -->
                    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateModalLabel">Add Tracking Update</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <label>Choose a tracking update</label>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="status1">
                                                <label for="status1">Order Placed</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="status2">
                                                <label for="status2">Shipment Scheduled</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="status3">
                                                <label for="status3">Shipment Confirmed</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="status4">
                                                <label for="status4">Warehouse Received</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="status5">
                                                <label for="status5">Out For Delivery</label>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-6"><label>Cancel Order</label></div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="status4">
                                                <label for="status4">Order cancelled</label>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-6"><label>add Shippment Remarks</label></div>
                                        <div class="col-6">
                                            <div class="form-input">
                                                <textarea class="form-control" cols="4" rows="5"></textarea>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary">Save Update</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


        </div>
    </div>

@endsection
