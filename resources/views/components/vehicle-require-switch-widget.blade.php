@if($job->job_status != 0 || auth()->user()->id != $job->user_id)
    <p>{{ ((int) $job->is_vehicle_require === 1 ? 'Yes' : 'No') }}</p>
@else
    <div class="form-check form-switch">
        <style>
            /* Default switch colors */
            .form-check-input {
                background-color: #ccc;
                /* Default background */
                border-color: #aaa;
            }

            /* Checked (ON) state */
            .form-check-input:checked {
                background-color: #28a745 !important;
                /* Green when ON */
                border-color: #28a745 !important;
            }

            /* Customize switch handle (thumb) */
            .form-check-input:checked::before {
                background-color: white;
            }

            /* Hover effect */
            .form-check-input:hover {
                cursor: pointer;
            }
        </style>
        @if(!empty($job->vehicleBookings->id))
            <span class="badge rounded-pill text-bg-info">Already Booked</span>
        @else
            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" data-id="{{ $job->id }}" {{ $job->is_vehicle_require ? 'checked' : '' }}>
            <label class="form-check-label" style="color:#fff;" for="flexSwitchCheckChecked"
                id="switchLabel">{{ (int) $job->is_vehicle_require === 1 ? 'Yes' : 'No' }}</label>
        @endif
    </div>



    <script>
        document.getElementById("flexSwitchCheckChecked").addEventListener("change", function () {
            let label = document.getElementById("switchLabel");
            label.textContent = this.checked ? "Yes" : "No";
        });
    </script>
    <script>

        $(document).ready(function () {
            $(".form-check-input").on("change", function () {
                let jobId = $(this).data("id");
                let isRequire = $(this).prop("checked") ? 1 : 0; // Get new status

                $.ajax({
                    url: "{{ route('v1.job-assignment-form.update-vehicle-require') }}",  // Update this with your actual route
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}", // CSRF protection
                        id: jobId,
                        is_vehicle_require: isRequire
                    },
                    success: function (response) {
                        showToast("Vehicle requirement updated successfully!", "success");
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    },
                    error: function () {
                        showToast("Error updating vehicle requirement!", "error");
                    }
                });
            });
        });
    </script>
@endif
