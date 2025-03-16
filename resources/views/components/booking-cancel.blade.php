<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
</div>

<div class="modal fade" id="cancelConfirmModal" tabindex="-1" aria-labelledby="cancelConfirmModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelConfirmModalLabel">Confirm Cancellation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel this booking?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger confirm-cancel-booking" data-id="">Confirm</button>
            </div>
        </div>
    </div>
</div>
<script>
    // Set the booking ID in the confirmation modal
    $(document).on('click', '.cancel-booking', function () {
        var bookingId = $(this).data('id');
        $('.confirm-cancel-booking').data('id', bookingId);
    });

    // Confirm cancellation and send AJAX request
    $(document).on('click', '.confirm-cancel-booking', function () {
        var bookingId = $(this).data('id');
        $.ajax({
            url: '/v1/vehicle-bookings/' + bookingId + '/cancel',
            type: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#cancelConfirmModal').modal('hide');
                showToast("Vehicle booking cancelled successfully!", "success"); // Show toast
                //reload datatable in here
                window.dispatchEvent(new Event("reloadDataTable"));
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                alert('Error cancelling booking.');
            }
        });
    });
</script>
