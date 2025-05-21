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
 @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Oops! Something went wrong:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
            <div class="card">
                <form method="POST" action="{{ route('v1.client-database.client-update-request', $client->id) }}">
    @csrf
    @method('PUT')
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>&nbsp;</th>
                                <td>Current</td>
                                <td>Request to Edit</td>
                            </tr>
                            <tr>
                                <th>Image</th>
                                <td>@if ($client->image_path)
                                    <img src="{{ asset('storage/' . $client->image_path) }}" alt="Client Image"
                                        class="img-fluid rounded shadow" style="max-height: 200px;">
                                @else
                                                    <svg fill="#000000" width="150px" height="150px" viewBox="0 0 32 32"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <defs>
                                                            <style>
                                                                .cls-1 {
                                                                    fill: none;
                                                                }
                                                            </style>
                                                        </defs>
                                                        <title>no-image</title>
                                                        <path d="M30,3.4141L28.5859,2,2,28.5859,3.4141,30l2-2H26a2.0027,2.0027,0,0,0,2-2V5.4141ZM26,26H7.4141l7.7929-7.793,
                                        2.3788,2.3787a2,2,0,0,0,2.8284,0L22,19l4,3.9973Zm0-5.8318-2.5858-2.5859a2,2,0,0,0-2.8284,
                                        0L19,19.1682l-2.377-2.3771L26,7.4141Z" />
                                                        <path d="M6,22V19l5-4.9966,1.3733,1.3733,1.4159-1.416-1.375-1.375a2,2,0,0,0-2.8284,
                                        0L6,16.1716V6H22V4H6A2.002,2.002,0,0,0,4,6V22Z" />
                                                        <rect class="cls-1" width="32" height="32" />
                                                    </svg>
                                    @endif
                                </td>
                                <td>@if ($client->image_path)
                                    <img src="{{ asset('storage/' . $clientRequest->image_path) }}" alt="Client Image"
                                        class="img-fluid rounded shadow" style="max-height: 200px;">
                                @else
                                                    <svg fill="#000000" width="150px" height="150px" viewBox="0 0 32 32"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <defs>
                                                            <style>
                                                                .cls-1 {
                                                                    fill: none;
                                                                }
                                                            </style>
                                                        </defs>
                                                        <title>no-image</title>
                                                        <path d="M30,3.4141L28.5859,2,2,28.5859,3.4141,30l2-2H26a2.0027,2.0027,0,0,0,2-2V5.4141ZM26,26H7.4141l7.7929-7.793,
                                        2.3788,2.3787a2,2,0,0,0,2.8284,0L22,19l4,3.9973Zm0-5.8318-2.5858-2.5859a2,2,0,0,0-2.8284,
                                        0L19,19.1682l-2.377-2.3771L26,7.4141Z" />
                                                        <path d="M6,22V19l5-4.9966,1.3733,1.3733,1.4159-1.416-1.375-1.375a2,2,0,0,0-2.8284,
                                        0L6,16.1716V6H22V4H6A2.002,2.002,0,0,0,4,6V22Z" />
                                                        <rect class="cls-1" width="32" height="32" />
                                                    </svg>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ $client->name }}</td>
                                <td style="{{ $clientRequest->name != $client->name ? 'color: blue;' : '' }}">
                                    {{ $clientRequest->name }}
                                    <input type="hidden" name="name" value="{{ $clientRequest->name }}">
                                </td>
                            </tr>
                            <tr>
                                <th>Company</th>
                                <td>{{ $client->company }}</td>
                                <td style="{{ $clientRequest->company != $client->company ? 'color: blue;' : '' }}">
                                    {{ $clientRequest->company }}
                                    <input type="hidden" name="company" value="{{ $clientRequest->company }}">
                                </td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $client->email }}</td>
                                <td style="{{ $clientRequest->email != $client->email ? 'color: blue;' : '' }}">
                                    {{ $clientRequest->email }}
                                    <input type="hidden" name="email" value="{{ $clientRequest->email }}">
                                </td>
                            </tr>
                            <tr>
                                <th>Office</th>
                                <td>{{ $client->office_number }}</td>
                                <td
                                    style="{{ $clientRequest->office_number != $client->office_number ? 'color: blue;' : '' }}">
                                    {{ $clientRequest->office_number }}
                                    <input type="hidden" name="office_number" value="{{ $clientRequest->office_number }}">
                                </td>
                            </tr>
                            <tr>
                                <th>Mobile</th>
                                <td>{{ $client->mobile_number }}</td>
                                <td
                                    style="{{ $clientRequest->mobile_number != $client->mobile_number ? 'color: blue;' : '' }}">
                                    {{ $clientRequest->mobile_number }}
                                     <input type="hidden" name="mobile_number" value="{{ $clientRequest->mobile_number }}">
                                </td>
                            </tr>
                            <tr>
                                <th>Job Title</th>
                                <td>{{ $client->job_title }}</td>
                                <td
                                    style="{{ $clientRequest->job_title != $client->job_title ? 'color: blue;' : '' }}">
                                    {{ $clientRequest->job_title }}
                                    <input type="hidden" name="job_title" value="{{ $clientRequest->job_title }}">
                                </td>
                            </tr>
                            <tr>
                                <th>Industry</th>
                                <td>{{ $client->industryCategory->name ?? '-' }}</td>
                                <td
                                    style="{{ $clientRequest->industry_category_id != $client->industry_category_id ? 'color: blue;' : '' }}">
                                    {{ $clientRequest->industryCategory->name }}
                                    <input type="hidden" name="industry_category_id" value="{{ $clientRequest->industry_category_id }}">
                                </td>
                            </tr>
                            <tr>
                                <th>Country</th>
                                <td>{{ $client->country->name ?? '-' }}</td>
                                <td
                                    style="{{ $clientRequest->country_id != $client->country_id ? 'color: blue;' : '' }}">
                                    {{ $clientRequest->country->name }}
                                    <input type="hidden" name="country_id" value="{{ $clientRequest->country_id }}">
                                </td>
                            </tr>
                            <tr>
                                <th>Sales Person</th>
                                <td>{{ $client->salesPerson->name ?? '-' }}</td>
                                <td
                                    style="{{ $clientRequest->sales_person_id != $client->sales_person_id ? 'color: blue;' : '' }}">
                                    {{ $clientRequest->salesPerson->name ?? '-' }}
                                    <input type="hidden" name="sales_person_id" value="{{ $clientRequest->sales_person_id }}">
                                </td>
                            </tr>
                            <tr>
                                <th></th>
                                <td colspan="2">
                                    <a href="{{ route('v1.client-database.request-list') }}" class="btn btn-warning">Cancel</a>&nbsp;<button class="btn btn-success" type="submit">Update</button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </form>
                </div>
            </div>
        </div>
@endsection
