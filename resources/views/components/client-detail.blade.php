<div class="modal fade" id="clientDetailModal" tabindex="-1" aria-labelledby="clientDetailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clientDetailModalLabel">Client Detail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="client-detail-body">
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(document).on('click', '.view-client', function () {
                    const clientId = $(this).data('id');
                    $('#client-detail-body').html('<p>Loading...</p>');
                    $('#clientDetailModal').modal('show');

                    $.get('/v1/client-database/' + clientId + '/detail', function (data) {
                        $('#client-detail-body').html(data);
                    }).fail(function () {
                        $('#client-detail-body').html('<p class="text-danger">Failed to load client data.</p>');
                    });
            });
        </script>