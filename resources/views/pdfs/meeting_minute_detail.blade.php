<!DOCTYPE html>
<html>
<head>
    <title>Meeting Minutes: {{ $minute->topic }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2, h3, h4, h5, h6 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #0056b3;
        }
        .header-section {
            border-bottom: 2px solid #0056b3;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .detail-item {
            margin-bottom: 10px;
        }
        .detail-item strong {
            display: inline-block;
            width: 120px; /* Adjust as needed for alignment */
            vertical-align: top;
        }
        .attendee-section {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .attendee-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        .attendee-card h6 {
            margin-bottom: 5px;
            color: #555;
        }
        .attendee-card p {
            margin-bottom: 0;
            white-space: pre-wrap; /* Preserve whitespace and line breaks */
        }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #eee;
            padding-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-section">
            <h1>Meeting Minutes</h1>
            <h3>{{ $minute->topic }}</h3>
        </div>

        <div class="main-details">
            <div class="detail-item">
                <strong>Date:</strong> {{ $minute->meeting_date->format('d M Y') }}
            </div>
            <div class="detail-item">
                <strong>Time:</strong> {{ $minute->start_time->format('H:i') }} - {{ $minute->end_time->format('H:i') }}
            </div>
            <div class="detail-item">
                <strong>Recorded By:</strong> {{ $minute->savedBy->name ?? 'N/A' }}
            </div>
            <div class="detail-item">
                <strong>Recorded At:</strong> {{ $minute->created_at->format('d M Y H:i') }}
            </div>
        </div>

        <div class="attendee-section">
            <h2>Attendees & Notes</h2>
            @forelse($minute->attendees as $attendee)
                <div class="attendee-card">
                    <h6>{{ $attendee->user->name ?? 'Unknown Attendee' }}</h6>
                    <p>{{ $attendee->speaker_notes ?? 'No notes recorded.' }}</p>
                </div>
            @empty
                <p>No attendees recorded for this meeting.</p>
            @endforelse
        </div>

        <div class="footer">
            Generated on {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
        </div>
    </div>
</body>
</html>