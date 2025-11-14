@if($post->data_status == 0)
    @can('delete-management-memo')
        <form class="row g-3" id="restoreForm{{ $post->id }}"
            action="{{ route('v1.management-memo.destroy', ['id' => $post->id, 'status' => 1]) }}" method="post">
            @csrf

            @method('PUT')
        </form>
        <!-- Modal -->
        <div class="modal fade" id="confirmRestoreModal{{ $post->id }}" tabindex="-1" aria-labelledby="confirmDeleteLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Restore</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to restore this Management memo archives?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success"
                            onclick="document.getElementById('restoreForm{{ $post->id }}').submit();">Yes, Restore it</button>
                    </div>
                </div>
            </div>
        </div>



    @endcan
@endif
