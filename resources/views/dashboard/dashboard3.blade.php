@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <link rel="stylesheet" href="../assets/vendor/libs/fullcalendar/fullcalendar.css" />

    <link rel="stylesheet" href="../assets/vendor/css/pages/app-calendar.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        .callout {
  padding: 15px;
  border-left: 5px solid #80e491;
  background-color: #e7edfd;
  margin-bottom: 0px;
  border-radius: 4px;
}
.callout-event {
  padding: 15px;
  border-left: 5px solid #80e491;
  background-color: #defbe3;
  margin-bottom: 0px;
  border-radius: 4px;
}
.callout h5 {
  margin-top: 0;
  font-weight: bold;
}
</style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- custom-icon Breadcrumb-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                @foreach ($breadcrumb as $item)
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0);">{{ $item }}</a>
                        <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                    </li>
                @endforeach
            </ol>
        </nav>

        <h3>{{ $title }}</h3>
        <button type="button" class="btn btn-primary view-booking"
        data-id="2"
        data-bs-toggle="modal"
        data-bs-target="#bookingModal">
    View Booking
</button>
<!-- Vehicle Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Vehicle Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img id="bookingImage" src="" alt="Vehicle Image" class="img-fluid rounded" style="max-width: 300px;">
                </div>
                <p><strong>Vehicle:</strong> <span id="bookingVehicle"></span></p>
                <p><strong>Start Date:</strong> <span id="bookingStart"></span></p>
                <p><strong>End Date:</strong> <span id="bookingEnd"></span></p>
                <p><strong>Purposes:</strong> <span id="bookingPurpose"></span></p>
                <p><strong>Job Assignment:</strong> <span id="bookingJob"></span></p>
                <p><strong>Created By:</strong> <span id="bookingCreator"></span></p>
                <p><strong>Booker Status:</strong> <span id="bookingBooker"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
    $(".view-booking").click(function () {
        var bookingId = $(this).data("id");

        $.ajax({
            url: "/v1/vehicle-bookings/"+bookingId+"/modal", // Adjust the route as needed
            type: "GET",
            success: function (response) {
                $("#bookingImage").attr("src", response.vehicle.path_image ? "/storage/" + response.vehicle.path_image : "/images/default-car.jpg");
                $("#bookingVehicle").text(response.vehicle.name);
                $("#bookingStart").text(response.start_at);
                $("#bookingEnd").text(response.end_at);
                $("#bookingPurpose").text(response.purposes);
                $("#bookingJob").text(response.job_assignment ? response.job_assignment.scope_of_work : "N/A");
                $("#bookingCreator").text(response.creator ? response.creator.name : "Unknown");
                $("#bookingBooker").text(response.is_booker ? "Yes" : "No");
            },
            error: function () {
                alert("Error fetching booking details.");
            }
        });
    });
});

    </script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        @endsection
