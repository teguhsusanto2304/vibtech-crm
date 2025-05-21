<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clients PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<h2>Client List</h2>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Company</th>
            <th>Email</th>
            <th>Office</th>
            <th>Mobile</th>
            <th>Job Title</th>
            <th>Industry</th>
            <th>Country</th>
            <th>Sales Person</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($clients as $client)
            <tr>
                <td>{{ $client->name }}</td>
                <td>{{ $client->company }}</td>
                <td>{{ $client->email }}</td>
                <td>{{ $client->office_number }}</td>
                <td>{{ $client->mobile_number }}</td>
                <td>{{ $client->job_title }}</td>
                <td>{{ $client->industryCategory->name ?? '-' }}</td>
                <td>{{ $client->country->name ?? '-' }}</td>
                <td>{{ $client->salesPerson->name ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
