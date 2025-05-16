<div>
    <h5>{{ $client->name }}</h5>
    <p><strong>Company:</strong> {{ $client->company }}</p>
    <p><strong>Email:</strong> {{ $client->email }}</p>
    <p><strong>Office:</strong> {{ $client->office_number }}</p>
    <p><strong>Mobile:</strong> {{ $client->mobile_number }}</p>
    <p><strong>Job Title:</strong> {{ $client->job_title }}</p>
    <p><strong>Industry:</strong> {{ $client->industryCategory->name ?? '-' }}</p>
    <p><strong>Country:</strong> {{ $client->country->name ?? '-' }}</p>
    <p><strong>Sales Person:</strong> {{ $client->salesPerson->name ?? '-' }}</p>
</div>
