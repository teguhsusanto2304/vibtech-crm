<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Vehicle Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Vehicle Image -->

                    <!-- Booking Details -->
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-5"><strong>Vehicle</strong></div>
                            <div class="col-md-1">:</div>
                            <div class="col-md-6"><span id="bookingVehicle"></span></div>
                        </div>
                        <div class="row">
                            <div class="col-md-5"><strong>Start Date</strong></div>
                            <div class="col-md-1">:</div>
                            <div class="col-md-6"><span id="bookingStart"></span></div>
                        </div>
                        <div class="row">
                            <div class="col-md-5"><strong>End Date</strong></div>
                            <div class="col-md-1">:</div>
                            <div class="col-md-6"><span id="bookingEnd"></span></div>
                        </div>
                        <div class="row">
                            <div class="col-md-5"><strong>Purposes</strong></div>
                            <div class="col-md-1">:</div>
                            <div class="col-md-6"><span id="bookingPurpose"></span></div>
                        </div>
                        <div class="row">
                            <div class="col-md-5"><strong>Job Assignment</strong></div>
                            <div class="col-md-1">:</div>
                            <div class="col-md-6"><span id="bookingJob"></span></div>
                        </div>
                        <div class="row">
                            <div class="col-md-5"><strong>Created By</strong></div>
                            <div class="col-md-1">:</div>
                            <div class="col-md-6"><span id="bookingCreator"></span></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <img id="bookingImage" src="" alt="Vehicle Image" class="img-fluid rounded"
                            style="max-width: 300px;">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on("click", ".view-booking", function () {
                        let bookingId = $(this).data("id");
                        $.ajax({
                            url: "/v1/vehicle-bookings/" + bookingId + "/modal", // Adjust the route as needed
                            type: "GET",
                            success: function (response) {
                                $("#bookingImage").attr("src", response.vehicle.path_image ? "/" + response.vehicle.path_image : "/images/default-car.jpg");
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
</script>
