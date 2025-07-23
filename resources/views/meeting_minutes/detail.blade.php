@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom-icon">
                    @foreach ($breadcrumb as $item)
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);">{{ $item }}</a>
                            <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                        </li>
                    @endforeach
                </ol>
            </nav>

            <h3>{{ $title }}</h3>

            <!-- Back Button & Print Button -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('v1.meeting-minutes.list') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                <button type="button" class="btn btn-info print-page-btn">
                    <i class="fas fa-print"></i> Print Page
                </button>
            </div>

            <div id="msg" class="mb-3"></div> <!-- For general messages -->
        </div>

        <!-- Meeting Details Card -->
        <div class="card mb-4">
            
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Topic:</strong> {{ $meetingMinute->topic }}
                    </div>
                    <div class="col-md-6">
                        <strong>Date:</strong> {{ $meetingMinute->meeting_date->format('d M Y') }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Time:</strong> {{ $meetingMinute->start_time->format('H:i') }} - {{ $meetingMinute->end_time->format('H:i') }}
                    </div>
                    <div class="col-md-6">
                        <strong>Saved By:</strong> {{ $meetingMinute->savedBy->name ?? 'N/A' }}
                    </div>
                </div>

                <hr class="my-4">

                <h5>Attendees and Notes:</h5>
                @forelse($meetingMinute->attendees as $attendee)
                    <div class="mb-3 p-3 border rounded bg-light">
                        <div class="d-flex align-items-center mb-2">
                            <img src="{{ $attendee->user->avatar_url ?? 'https://placehold.co/40x40/cccccc/333333?text=N/A' }}" alt="{{ $attendee->user->name ?? 'N/A' }} Avatar" class="rounded-circle me-2" width="30" height="30">
                            <h6 class="mb-0">{{ $attendee->user->name ?? 'Unknown Attendee' }} Notes:</h6>
                        </div>
                        <p class="mb-0">{!! nl2br(e($attendee->speaker_notes ?? 'No notes recorded for this attendee.')) !!}</p>
                    </div>
                @empty
                    <p class="text-muted">No attendees recorded for this meeting.</p>
                @endforelse
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {
            // --- Print Page Logic ---
            $('.print-page-btn').on('click', function() {
                window.print(); // Triggers the browser's print dialog
            });
        });
    </script>

    <!-- CSS for Print Media Queries -->
    <style>
        /* Hide elements when printing */
        @media print {
            /* Hide navigation, sidebar, header, footer, and buttons */
            .layout-navbar,
            .layout-menu,
            .content-footer,
            .breadcrumb,
            .print-page-btn, /* Hide the print button itself */
            .d-flex.justify-content-between.align-items-center.mb-3 .btn-secondary, /* Hide the back button */
            .modal-backdrop, /* Hide modal backdrop if a modal is open */
            .modal { /* Hide modals if they are open */
                display: none !important;
            }

            /* Adjust layout for printing */
            body {
                margin: 0;
                padding: 0;
                color: #000; /* Ensure text is black for print */
            }

            .container-xxl {
                width: 100% !important; /* Make container full width */
                max-width: none !important; /* Remove max-width constraints */
                padding: 0 !important; /* Remove padding */
            }

            .card {
                border: 1px solid #ccc !important; /* Add subtle border to cards */
                box-shadow: none !important; /* Remove shadows */
                margin-bottom: 1rem !important; /* Add margin between cards */
            }

            .card-header {
                background-color: #f0f0f0 !important; /* Light background for headers */
                color: #000 !important; /* Ensure header text is black */
                border-bottom: 1px solid #ccc !important;
            }

            .table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .table th, .table td {
                border: 1px solid #ccc !important; /* Ensure table borders are visible */
                padding: 8px !important;
            }

            /* Ensure text is readable */
            h1, h2, h3, h4, h5, h6, p, li, span {
                color: #000 !important;
            }
        }
    </style>
@endsection