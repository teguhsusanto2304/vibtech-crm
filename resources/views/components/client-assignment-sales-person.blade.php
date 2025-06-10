<div class="modal fade" id="clientDetailAssignModal" tabindex="-1" aria-labelledby="clientDetailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clientDetailModalLabel">Salesperson Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="assignClientForm" 
                        action="{{ route('v2.client-database.assignment-salesperson')}}?main=yes">
                        @csrf
                        <div class="modal-body" id="client-detail-assign-body">
                            <p>Loading...</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                                aria-label="Close">No</button>&nbsp;
                            <button type="submit" class="btn btn-success">Yes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Login Modal -->
        <div class="modal fade" id="reassignLoginConfirmModal" tabindex="-1" aria-labelledby="loginConfirmLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="reassignLoginConfirmForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Your Identity</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="reassignLoginEmail" name="email"
                                    value="{{ auth()->user()->email }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="reassignLoginPassword" name="password" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Confirm</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            $(document).on('click', '.view-client-assign', function () {
                const clientId = $(this).data('id');
                $('#client-detail-assign-body').html('<p>Loading...</p>');
                $('#clientDetailAssignModal').modal('show');

                $.get('/v1/client-database/' + clientId + '/detail?assign=yes', function (data) {
                    $('#client-detail-assign-body').html(data);
                    $('#client_id').val(clientId);
                }).fail(function () {
                    $('#client-detail-assign-body').html('<p class="text-danger">Failed to load client data.</p>');
                });
            });

            let assignFormToSubmit;

                $(document).on("submit", "#assignClientForm", function (e) {
                    e.preventDefault(); // Stop normal form submission
                    assignFormToSubmit = "#assignClientForm";
                    $('#clientDetailAssignModal').modal('hide');
                    $("#reassignLoginConfirmModal").modal("show"); // Show the login modal
                });
                $("#reassignLoginConfirmForm").on("submit", function (e) {
                    e.preventDefault();

                    const email = $('#reassignLoginConfirmForm #reassignLoginEmail').val();
                    const password = $('#reassignLoginConfirmForm #reassignLoginPassword').val(); // Get password input value
                    const reassignCsrfToken = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        url: "{{ route('v1.login.verify') }}", // Create this route to check credentials
                        type: "POST",
                        data: {
                            email: email,
                            password: password,
                            _token: reassignCsrfToken
                        },
                        success: function (res) {
                            if (res.success) {
                                $("#reassignLoginConfirmModal").modal("hide");

                                // Submit the original form after login verified
                                if (assignFormToSubmit) {
                                    const formUrl = $(assignFormToSubmit).attr('action'); // Get action URL from the form
                                    let formData = $(assignFormToSubmit).serializeArray();
                                    formData.push({ name: "_token", value: reassignCsrfToken }); // Add CSRF token

                                    $.ajax({
                                        url: formUrl,
                                        method: "PUT",
                                        data: $.param(formData), 
                                        success: function (response) {
                                            if (response.success) {
                                                $('#clients-table').DataTable().ajax.reload(); // Reload DataTables to show changes
                                                showAlert(response.message, 'success'); // Show success alert
                                            } else {
                                                // Backend returned success: false (e.g., validation failed, or specific logic)
                                                showAlert(response.message || 'Operation failed. Please try again.', 'danger');
                                            }
                                        },
                                        error: function (xhr, status, error) {
                                            // AJAX error during assignment (e.g., 400, 500 status)
                                            let errorMessage = 'An error occurred during assignment.';
                                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                                errorMessage = xhr.responseJSON.message;
                                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n'); // Show validation errors
                                            }
                                            showAlert(errorMessage, 'danger');
                                            console.error("Assignment AJAX Error:", xhr);
                                        }
                                    });

                                } else {
                                    alert("No form found to submit after login verification.");
                                    console.error("assignFormToSubmit is null/undefined after login verification.");
                                }
                            } else {
                                alert("Login failed. Please try again.");
                            }
                        },
                        error: function () {
                            alert("An error occurred while verifying login.");
                        }
                    });
                });
            
            </script>