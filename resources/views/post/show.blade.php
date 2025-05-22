@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- custom-icon Breadcrumb-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom-icon">
                    @foreach ($breadcrumb as $item)
                        <li class="breadcrumb-item">
                            @if($item == 'Job Assignment Form')
                                <a href="{{ route('v1.job-assignment-form')}}">{{ $item }}</a>
                            @else
                                <a href="javascript:void(0);">{{ $item }}</a>
                            @endif
                            <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                        </li>
                    @endforeach
                </ol>
            </nav>

            <h3>{{ $title }}</h3>

            <div class="card">
                <div class="card-body">
                    <style>
                        .post-content {
                            font-family: 'Segoe UI', sans-serif;
                            line-height: 1.7;
                            font-size: 1rem;
                            color: #333;
                            padding: 1rem;
                            background-color: #f9f9f9;
                            border-radius: 8px;
                        }

                        .post-content h1, .post-content h2, .post-content h3 {
                            margin-top: 1rem;
                            margin-bottom: 0.5rem;
                        }

                        .post-content p {
                            margin-bottom: 1rem;
                        }

                        .post-content img {
                            max-width: 100%;
                            height: auto;
                            display: block;
                            margin: 1rem 0;
                        }

                        .post-content ul, .post-content ol {
                            margin-left: 1.5rem;
                            margin-bottom: 1rem;
                        }

                        .post-content blockquote {
                            border-left: 4px solid #ccc;
                            padding-left: 1rem;
                            color: #666;
                            font-style: italic;
                            margin: 1rem 0;
                        }
                    </style>


                    <div class="post-content">
                        <div class="row">
                            <div class="col-9">
                                {!! $post->content !!}
                            </div>
                            <div class="col-3">
                                {{-- Daftar Pengguna yang Sudah Membaca --}}
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Users Have Read</h5>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($readUsers as $reader)
                    {{ $reader->user->name }}<br>
                        {{-- Tampilkan waktu baca dari pivot table --}}
                        <small class="text-muted">Read at {{ $reader->read_at }}</small>
                @empty
                    <li class="list-group-item">No one has read this memo yet.</li>
                @endforelse
            </ul>
        </div>
                                <!-- show user unread -->
                                <div class="card mb-3">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">Users Haven't Read</h5>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($unreadUsers as $reader)
                    <p>{{ $reader->name }}</p>
                @empty
                    <li class="list-group-item">No one has read this memo yet.</li>
                @endforelse
            </ul>
        </div>
                            </div>
                        </div>
                        <hr style="border: #666">

                        @auth {{-- Only show if user is logged in --}}
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" value="1" id="readMemoCheckbox"
                                    data-post-id="{{ $post->id }}"
                                    {{ $userHasRead ? 'checked disabled' : '' }}>
                                <label class="form-check-label" for="readMemoCheckbox">
                                    I have already Read this memo
                                </label>
                            </div>
                        @endauth
                        @if($logs->count())
                        <div class="mt-4">
                            <h5>Update Logs</h5>
                            <ul class="list-group">
                                @foreach($logs as $log)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $log->user->name ?? 'Unknown User' }}</span>
                                        <small class="text-muted">{{ $log->created_at->format('d M Y H:i') }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    $(function() {
        // Event listener for the checkbox
        $('#readMemoCheckbox').on('change', function() {
            const checkbox = $(this);
            const postId = checkbox.data('post-id');
            const isChecked = checkbox.is(':checked');
            const csrfToken = $('meta[name="csrf-token"]').attr('content'); // Ensure you have this meta tag in your layout

            $.ajax({
                url: "{{ route('v1.management-memo.toggle-read-status', ['id' => ':id']) }}".replace(':id', postId),
                type: 'POST',
                data: {
                    _token: csrfToken,
                    is_read: isChecked ? 1 : 0 // Send 1 if checked, 0 if unchecked
                },
                success: function(response) {
                    console.log(response.message);
                    // Optionally, show a temporary success message to the user
                    // e.g., using Bootstrap's alert or a toast notification
                },
                error: function(xhr) {
                    console.error('Error updating read status:', xhr.responseText);
                    // Optionally, revert the checkbox state if the update failed
                    checkbox.prop('checked', !isChecked);
                    // Show an error message to the user
                }
            });
        });
    });
</script>
@endsection
