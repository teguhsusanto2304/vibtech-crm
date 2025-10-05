
<div class="btn-group" role="group" aria-label="Basic mixed styles example">
    @if((int) $job->user_id === (int) auth()->user()->id)
        @if((int) $job->is_publish != 1 && (int) $job->job_status != 4 && (int) $job->job_status != 3)
            <button type="button" class="btn btn-primary btn-md action-btn"
                    data-action="publish">Publish Job</button>
        @endif
        @if((int) $job->job_status == 4)
            <a href="{{ route('v1.job-assignment-form.edit', ['id' => $job->id])}}"
                class="btn btn-success">Edit Job</a>
        @endif
        @if((int) $job->job_status == 4)
            <button type="button" class="btn btn-danger btn-md action-btn"
                    data-action="cancel">Cancel Job</button>
        @endif
        @if((int) $job->job_status != 4 && (int) $job->job_status != 3)
            <button type="button" class="btn btn-warning btn-md action-btn"
                    data-action="recall">Recall Job</button>
        @endif
    @endif
</div>


<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="color: #131313">Are you sure you want to <span id="actionText"></span> this job requisition form?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmBtnStatus">Yes</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for handling the modal -->
<script>
    $(document).ready(function () {
        let action = "";

        $(".action-btn").on("click", function () {
            action = $(this).data("action");
            $("#actionText").text(action);
            $("#confirmModal").modal("show");
        });

        $("#confirmBtnStatus").off("click").on("click", function () {
            $.ajax({
                url: "{{ route('v1.job-assignment-form.history.update-status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: "{{ $job->id }}",
                    action: action,
                },
                success: function (response) {
                    if (response.success) {
                        if (action === "cancel") {
                            window.location.href = "{{ route('v1.job-assignment-form.history') }}";
                        } else {
                            location.reload();
                            showToast("Job assignment status updated successfully!", "success");
                        }
                    } else {
                        alert("Failed to update event.");
                    }
                },
                error: function () {
                    alert("Error updating event.");
                }
            });

            $("#confirmModal").modal("hide");
        });
    });
</script>
