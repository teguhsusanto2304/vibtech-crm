<div class="modal fade" id="clientDetailBulkRequestToEditModal" tabindex="-1" aria-labelledby="clientDetailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clientDetailModalLabel">Bulk Salesperson Request To Edit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="bulkRequestToEditClientForm" 
                        action="{{ route('v2.client-database.bulk-request-to-edit')}}">
                        <div class="form-group col-12" style="padding: 15px">
                            <label for="image_path">Add Reason</label>
                            <textarea class="form-control" name="edit_reason" id="edit_reason" cols="5"></textarea>
                        </div>
                        <input type="hidden" name="edit_client_ids" id="edit_client_ids">
                        <input type="hidden" name="edit_status" value="edit">
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
        <div class="modal fade" id="bulkRequestEditLoginConfirmModal" tabindex="-1" aria-labelledby="loginConfirmLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="bulkRequestEditLoginConfirmForm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Your Identity</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="RequestEditEmail" name="email"
                                    value="{{ auth()->user()->email }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="RequestEditPassword" name="password" required>
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
            let bulkRequestEditFormToSubmit;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function handleSubmitWithLoginCheckRequestEdit(event, formElement, modalToHide) {
                event.preventDefault(); // Stop normal form submission
                bulkRequestEditFormToSubmit = formElement; // Save reference to the form that will eventually submit
                $(modalToHide).modal('hide'); // Hide the original assignment modal
                $("#bulkRequestEditLoginConfirmModal").modal("show"); // Show the login modal
            }

            // Event handler for the Login Confirmation Form
            $("#bulkRequestEditLoginConfirmForm").on("submit", function (e) {
                e.preventDefault();
                const bulkReassignCsrfToken = $('meta[name="csrf-token"]').attr('content');

                // First, verify login credentials via AJAX
                $.ajax({
                    url: "{{ route('v1.login.verify') }}",
                    method: "POST",
                    data: $(this).serialize(), // Send username/password from the login modal
                    success: function (res) {
                        if (res.success) {
                            $("#bulkRequestEditLoginConfirmModal").modal("hide");
                            $("#clientDetailBulkRequestToEditModal").modal("show"); // Hide login modal

                            // If login is successful, proceed to submit the original form via AJAX
                            if (bulkRequestEditFormToSubmit) {
                                const formUrl = $(bulkRequestEditFormToSubmit).attr('action');
                                $.ajax({
                                    url: formUrl, // Action URL of the original form
                                    type: "PUT",
                                    data: {
                                        edit_client_ids: $('#client_ids').val()
                                    },// All form data including hidden fields
                                    success: function (response) {
                                        if (response.success) {
                                            $('#clients-table').DataTable().ajax.reload(); // Reload DataTables
                                            showAlert(response.message, 'success'); // Show success alert
                                        } else {
                                            showAlert(response.message || 'Operation failed. Please try again.', 'danger');
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        let errorMessage = 'An error occurred during assignment.';
                                        if (xhr.responseJSON && xhr.responseJSON.message) {
                                            errorMessage = xhr.responseJSON.message;
                                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                                        }
                                        showAlert(errorMessage, 'danger');
                                        console.error("Assignment AJAX Error:", xhr);
                                    }
                                });

                                
                            } else {
                                showAlert("No form found to submit after login verification.", 'danger');
                                console.error("assignFormToSubmit is null/undefined after login verification.");
                            }
                        } else {
                            showAlert(res.message || "Login failed. Please check your credentials.", 'danger');
                            $('#bulkReassignLoginConfirmForm #password').val('');
                        }
                    },
                    error: function (xhr, status, error) {
                        let errorMessage = "Login verification failed due to a server error.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showAlert(errorMessage, 'danger');
                        console.error("Login Verify AJAX Error:", xhr);
                    }
                });
            });
            $(document).on("submit", "#bulkRequestEditClientForm", function (e) {
                // 'e' is the event object, 'this' is the #assignClientForm HTML element
                // '#clientDetailAssignModal' is the ID of the modal containing this form
                handleSubmitWithLoginCheck(e, this, '#clientDetailBulkRequestEditModal');
            });
            
            function handleBulkRequestEditAction(ids) {
                    $('#client-detail-assign-body').html('<p>Loading...</p>');
                    $('#clientDetailBulkRequestToEditModal').modal('show');

                    $.get('/v1/client-database/0/detail', function (data) {
                        $('#client-detail-bulk-assign-body').html(data);
                        $('#edit_client_ids').val(ids);
                    }).fail(function () {
                        $('#client-detail-assign-body').html('<p class="text-danger">Failed to load client data.</p>');
                    });
                }


            $('#edit-selected').on('click', function () {
                    let ids = getSelectedIds();
                    if (ids.length === 0) {
                        showCustomAlert('Please select at least one row to request to edit.', 'Selection Required');
                        return;
                    }
                    showConfirmationModal(
                        'Are you sure you want to request to edit the selected data?', // Message
                        'Confirm Request To Edit',                               // Title
                        function() {                                        // Callback function (executed if 'Yes' is clicked)
                            // This code will only run IF the user clicks 'Yes' in the modal
                            handleBulkRequestEditAction(ids); // Your existing function call
                            //alert('teguh');
                        }
                    );
                }); 
            </script>