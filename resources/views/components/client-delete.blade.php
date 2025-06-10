<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to <span id="actionText"></span> this client?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Modal -->
        <div class="modal fade" id="deleteLoginConfirmModal" tabindex="-1" aria-labelledby="loginConfirmLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="deleteLoginConfirmForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Your Identity</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="deleteLoginEmail" name="email"
                                    value="{{ auth()->user()->email }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="deleteLoginPassword" name="password" required>
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
            
            function showAlert(message, type = 'success') {
                const messageContainer = $('#msg'); // Use the standardized container
                // Clear previous messages
                messageContainer.empty();

                // Create the alert HTML
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                messageContainer.append(alertHtml);

                // Optional: Auto-hide the alert after a few seconds
                // setTimeout(function() {
                //     messageContainer.find('.alert').alert('close'); // Requires Bootstrap JS
                // }, 5000); // 5 seconds
            }
            $(document).on("click", ".confirm-action", function () {
                    userId = $(this).data("id");
                    // Set action text in modal
                    $("#actionText").text("delete");

                    // Show modal
                    $("#confirmModal").modal("show");
                });
                let userIdToToggle = null;

                $("#confirmBtn").on("click", function () {
                    userIdToToggle = userId; // Save userId globally
                    $("#confirmModal").modal("hide");
                    $("#deleteLoginPassword").val('');
                    $("#deleteLoginConfirmModal").modal("show"); // Show login modal first
                });

                $("#deleteLoginConfirmForm").on("submit", function (e) {
                    e.preventDefault();

                    const email = $('#deleteLoginConfirmForm #deleteLoginEmail').val();
                    const password = $('#deleteLoginConfirmForm #deleteLoginPassword').val(); // Get password input value
                    const deleteCsrfToken = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        url: "{{ route('v1.login.verify') }}", // Create this route to check credentials
                        type: "POST",
                        data: {
                            email: email,
                            password: password,
                            _token: deleteCsrfToken
                        },
                        success: function (res) {
                            if (res.success) {
                                $("#deleteLoginConfirmModal").modal("hide");

                                // Proceed with toggle status
                                $.ajax({
                                    url: "{{ route('v1.client-database.toggle-status') }}",
                                    type: "POST",
                                    data: {
                                        _token: deleteCsrfToken,
                                        id: userIdToToggle
                                    },
                                    success: function (response) {
                                        if (response.success) {
                                            $('#confirmModal').modal('hide');
                                            $('#clients-table').DataTable().ajax.reload();
                                            showAlert(response.message || "Status Deleted successfully!", 'success');
                                        } else {
                                            alert("Failed to " + actionType + " user.");
                                        }
                                    },
                                    error: function () {
                                        showAlert(response.message || "Status Deleted unsuccessfully!", 'errors');
                                        alert("An error occurred.");
                                    }
                                });

                            } else {
                                alert("Login failed. Please check your credentials.");
                            }
                        },
                        error: function () {
                            alert("Login verification failed.");
                        }
                    });
                });
        </script>