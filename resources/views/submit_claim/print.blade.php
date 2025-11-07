<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Staff Expense Claim Invoice Preview</title>

    <!-- Use system-safe fonts for Dompdf -->
    <style>
        @page {
    margin: 30px 40px 30px 40px; /* top right bottom left */
}

body {
    background-color: #fff;
    font-family: 'Inter', sans-serif;
    font-size: 10pt;
}

.container {
    width: 750px;
}

.print-container {
    border: 1px solid #e5e7eb;
    box-shadow: none !important;
    padding: 2.5rem !important;
    margin-left: 20px; /* optional */
}

        h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 4px;
            color: #111827;
        }

        p {
            margin: 0;
            padding: 0;
        }

        .text-secondary {
            color: #6b7280;
        }

        .fw-bold { font-weight: 700; }
        .fw-medium { font-weight: 500; }
        .small { font-size: 9pt; }

        /* Header layout using table (since flex doesn’t render in Dompdf) */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .header-table td {
            vertical-align: top;
        }

        .header-logo {
            text-align: right;
            border: 1px solid #ddd;
            border-radius: 3px;
            width: 100px;
            height: 40px;
            line-height: 40px;
            font-size: 9pt;
            color: #6b7280;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 15px;
        }

        .info-table td {
            width: 25%;
            padding: 5px;
            vertical-align: top;
        }

        .info-table p {
            margin-bottom: 2px;
        }

        table.table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }

        table.table th, table.table td {
            border: 1px solid #e5e7eb;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        table.table th {
            background-color: #f3f4f6;
            font-weight: 600;
            color: #374151;
        }

        .text-end {
            text-align: right;
        }

        .bg-secondary {
            background-color: #f9fafb;
        }

        .total-section {
            margin-top: 15px;
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
        }

        .footer {
            text-align: center;
            font-size: 9pt;
            color: #6b7280;
            margin-top: 30px;
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
        }

        .footer p {
            margin-bottom: 3px;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="print-container">
        
        <header>
            <table class="header-table">
                <tr>
                    <td>
                        <h1>Staff Expense Claim</h1>
                        <p class="text-secondary">Invoice</p>
                    </td>
                    <td ></td>
                </tr>
            </table>

            <table class="info-table">
                <tr>
                    <td>
                        <p class="small text-secondary">Serial Number</p>
                        <p class="fw-medium">{{ $claim->serial_number }}</p>
                    </td>
                    <td>
                        <p class="small text-secondary">Claim Date</p>
                        <p class="fw-medium">{{ $claim->claim_date->format('d M Y') }}</p>
                    </td>
                    <td>
                        <p class="small text-secondary">Staff Name</p>
                        <p class="fw-medium">{{ $claim->staff->name ?? 'N/A' }}</p>
                    </td>
                    <td>
                        <p class="small text-secondary">Claim Title</p>
                        <p class="fw-medium">{{ $claim->description ?? 'N/A' }}</p>
                    </td>
                </tr>
            </table>
        </header>

        <main>
            <table class="table">
                <thead>
                <tr>
                    <th>Claim Type</th>
                    <th>Claim Purpose</th>
                    <th class="text-end">Period</th>
                    <th class="text-end">Submitted At</th>
                    <th class="text-end">Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach($groupedItems as $currencyCode => $itemsInGroup)
                    <tr class="bg-secondary">
                        <td colspan="5" class="fw-bold">
                            Transactions in {{ $currencyCode }}
                        </td>
                    </tr>
                    @foreach($itemsInGroup as $item)
                        <tr>
                            <td>{{ $item->claimType->name }}</td>
                            <td>{{ $item->description }}</td>
                            <td class="text-end">
                                <small>{{ $item->start_at->format('d M Y') }} - {{ $item->end_at->format('d M Y') }}</small>
                            </td>
                            <td class="text-end">
                                <small>{{ $item->created_at->format('d M Y H:i') }}</small>
                            </td>
                            <td class="text-end fw-medium">
                                {{ $item->currency . ' ' . number_format($item->amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>

            <div class="total-section">
                @foreach($groupedItems as $currencyCode => $itemsInGroup)
                    @php
                        $total = $itemsInGroup->sum('amount');
                    @endphp
                    <table width="100%">
                        <tr>
                            <td class="fw-bold">Total ({{ $currencyCode }})</td>
                            <td class="text-end fw-bold" style="color: #2563eb;">
                                {{ number_format($total, 2) }}
                            </td>
                        </tr>
                    </table>
                @endforeach
            </div>
        </main>

        <footer class="footer">
            <p class="fw-semibold">Vibtech Genesis</p>
            <p>60 Ubi Crescent, #01-05 Ubi Techpark, Singapore 408569</p>
            <p>Generated on {{ date('d M Y') }}</p>
        </footer>

    </div>
</div>
</body>
</html>
