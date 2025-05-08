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
                        {!! $post->content !!}
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
@endsection
