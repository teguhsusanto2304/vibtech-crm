@if($post->data_status == 1)

    @can('delete-management-memo')
        <form class="row g-3" id="deleteForm{{ $post->id }}"
            action="{{ route('v1.management-memo.destroy', ['id' => $post->id, 'status' => 0]) }}" method="post">
            @csrf

            @method('PUT')
        </form>
        <!-- Modal -->
        <div class="modal fade" id="confirmDeleteModal{{ $post->id }}" tabindex="-1" aria-labelledby="confirmDeleteLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Archive</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to archive this Management memo?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-warning"
                            onclick="document.getElementById('deleteForm{{ $post->id }}').submit();">Yes, Archive it</button>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endif
