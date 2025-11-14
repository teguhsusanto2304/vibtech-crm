@if($post->data_status == 0)
    @can('delete-management-memo')

        <form class="row g-3" id="permanentDeleteForm{{ $post->id }}"
            action="{{ route('v1.management-memo.destroy', ['id' => $post->id, 'status' => 3]) }}" method="post">
            @csrf

            @method('PUT')
        </form>
        <!-- Modal -->
        <div class="modal fade" id="confirmPermanentDeleteModal{{ $post->id }}" tabindex="-1"
            aria-labelledby="confirmDeleteLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Permanent Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to permanent delete this Management memo archives?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger"
                            onclick="document.getElementById('permanentDeleteForm{{ $post->id }}').submit();">Yes, Delete
                            it</button>
                    </div>
                </div>
            </div>
        </div>

    @endcan
@endif
