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
                        .content-divider {
    border: 5px solid #65646448; /* Example: solid gray line */
    margin-top: 20px;
    margin-bottom: 20px;
}

/* More elaborate divider */
.content-divider.fancy {
    border: 0;
    height: 5px;
    background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));
    margin-top: 30px;
    margin-bottom: 30px;
}
                    </style>


                    <div class="post-content">
                        {{ $post->content }}
                        <hr style="border: #666">

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="readMemoCheckbox"
                                data-post-id="{{ $post->id }}"
                                {{ $userHasRead ? 'checked' : '' }}>
                            <label class="form-check-label" for="readMemoCheckbox">
                                I have already Read this memo
                            </label>
                        </div>

                        @if($logs->count())
                            <div class="mt-4">
                                <h4>Update Logs</h4>
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
@endsection
