<script>
    $('#delete-selected').on('click', function () {
                    let ids = getSelectedIds();
                    if (ids.length === 0) {
                        showCustomAlert('Please select at least one row to delete.', 'Selection Required');
                        return;
                    }
                    showConfirmationModal(
                        'Are you sure you want to delete the selected data?', // Message
                        'Confirm Delete',                               // Title
                        function() {                                        // Callback function (executed if 'Yes' is clicked)
                            $("#bulkReassignLoginConfirmModal").modal("show");
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
                                            $("#bulkReassignLoginConfirmModal").modal("hide");
                                            $.ajax({
                                                url: '{{ route('v2.client-database.bulk-delete') }}', // Laravel route for bulk delete
                                                method: 'DELETE', // Use DELETE method for RESTfulness
                                                data: {
                                                    ids: ids, // Send the array of IDs
                                                    _token: '{{ csrf_token() }}' // Laravel CSRF token for security
                                                },
                                                success: function(response) {
                                                    // Handle success response
                                                    if (response.success) {
                                                        showAlert(response.message, 'success');
                                                        $('#clients-table').DataTable().ajax.reload();  // Or reload the entire page
                                                    } else {
                                                        showAlert(response.message || 'Operation failed. Please try again.', 'danger');
                                                    }
                                                },
                                                error: function(xhr) {
                                                    // Handle error response
                                                    let errorMessage = 'An error occurred during deletion.';
                                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                                        errorMessage = xhr.responseJSON.message;
                                                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                                        // Handle validation errors if any
                                                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                                                    }
                                                    showAlert(errorMessage, 'danger');
                                                    console.error('Bulk Delete Error:', xhr);
                                                }
                                            });
                                        }
                                    }
                                });
                            });
                        }
                    )
            });
    </script>