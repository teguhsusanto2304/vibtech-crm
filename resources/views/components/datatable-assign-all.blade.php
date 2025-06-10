<div class="modal fade" id="clientDetailBulkAssignModal" tabindex="-1" aria-labelledby="clientDetailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clientDetailModalLabel">Bulk Salesperson Reassignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="bulkAssignClientForm" 
                        action="{{ route('v2.client-database.bulk-assignment-salesperson')}}">                      
                        <div class="modal-body" id="client-detail-bulk-assign-body">
                            <p>Loading...</p>
                        </div>
                        <input type="hidden" name="client_ids" id="client_ids">
                        <input type="hidden" name="status" value="reassign">
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
        <div class="modal fade" id="bulkReassignLoginConfirmModal" tabindex="-1" aria-labelledby="loginConfirmLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="bulkReassignLoginConfirmForm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Your Identity</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="loginEmail" name="email"
                                    value="{{ auth()->user()->email }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="loginPassword" name="password" required>
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
            let bulkAssignFormToSubmit;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function handleSubmitWithLoginCheck(event, formElement, modalToHide) {
                event.preventDefault(); // Stop normal form submission
                bulkAssignFormToSubmit = formElement; // Save reference to the form that will eventually submit
                $(modalToHide).modal('hide'); // Hide the original assignment modal
                $("#bulkReassignLoginConfirmModal").modal("show"); // Show the login modal
            }

            // Event handler for the Login Confirmation Form
            $("#bulkReassignLoginConfirmForm").on("submit", function (e) {
                e.preventDefault();
                const bulkReassignCsrfToken = $('meta[name="csrf-token"]').attr('content');

                // First, verify login credentials via AJAX
                $.ajax({
                    url: "{{ route('v1.login.verify') }}",
                    method: "POST",
                    data: $(this).serialize(), // Send username/password from the login modal
                    success: function (res) {
                        if (res.success) {
                            $("#bulkReassignLoginConfirmModal").modal("hide"); // Hide login modal

                            // If login is successful, proceed to submit the original form via AJAX
                            if (bulkAssignFormToSubmit) {
                                const formUrl = $(bulkAssignFormToSubmit).attr('action');

                                $.ajax({
                                    url: formUrl, // Action URL of the original form
                                    type: "PUT",
                                    data: {
                                        client_ids: $('#client_ids').val(),
                                        sales_person_id: $('#sales_person_id').val(),
                                        status:"reassign"
                                    },// All form data including hidden fields
                                    success: function (response) {
                                        if (response.success) {
                                            $('#clientDetailAssignModal').modal('hide'); // Hide single assign modal
                                            $('#clientDetailBulkAssignModal').modal('hide'); // Hide bulk assign modal
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
            $(document).on("submit", "#bulkAssignClientForm", function (e) {
                // 'e' is the event object, 'this' is the #assignClientForm HTML element
                // '#clientDetailAssignModal' is the ID of the modal containing this form
                handleSubmitWithLoginCheck(e, this, '#clientDetailBulkAssignModal');
            });
            
            function handleBulkAction(ids) {
                    $('#client-detail-assign-body').html('<p>Loading...</p>');
                    $('#clientDetailBulkAssignModal').modal('show');

                    $.get('/v1/client-database/0/detail', function (data) {
                        $('#client-detail-bulk-assign-body').html(data);
                        $('#client_ids').val(ids);
                    }).fail(function () {
                        $('#client-detail-assign-body').html('<p class="text-danger">Failed to load client data.</p>');
                    });
                }


            $('#reassign-selected').on('click', function () {
                    let ids = getSelectedIds();
                    if (ids.length === 0) {
                        showCustomAlert('Please select at least one row to start assignment.', 'Selection Required');
                        return;
                    }
                    showConfirmationModal(
                        'Are you sure you want to assign the selected data?', // Message
                        'Confirm Assignment',                               // Title
                        function() {                                        // Callback function (executed if 'Yes' is clicked)
                            // This code will only run IF the user clicks 'Yes' in the modal
                            handleBulkAction(ids); // Your existing function call
                            //alert('teguh');
                        }
                    );
                }); 
            </script>