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
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

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
            <ul class="nav nav-tabs mb-3" id="leaveTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="manual-tab"
                data-bs-toggle="tab"
                data-bs-target="#manual"
                type="button"
                role="tab">
            Manual Input
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link active"
                id="upload-tab"
                data-bs-toggle="tab"
                data-bs-target="#upload"
                type="button"
                role="tab">
            Upload Excel
        </button>
    </li>
</ul>
<div class="tab-content" id="leaveTabsContent">

    <!-- ================= TAB 1: MANUAL INPUT ================= -->
    <div class="tab-pane fade show active"
         id="manual"
         role="tabpanel"
         aria-labelledby="manual-tab">

        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                Manual Leave Application
            </div>

            <div class="card-body">
                <form action="{{ route('v1.leave-application.store') }}" method="POST">
                    @csrf

                    <!-- Country -->
                    <div class="mb-3">
                        <label class="form-label">Country</label>
                        <select name="country_code" class="form-select" required>
                            <option value="">-- Select Country --</option>
                            <option value="SG">Singapore</option>
                            <option value="MY">Malaysia</option>
                        </select>
                    </div>

                    <!-- Leave Date -->
                    <div class="mb-3">
                        <label class="form-label">Leave Date</label>
                        <input type="date"
                               name="leave_date"
                               class="form-control"
                               required>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               maxlength="150"
                               required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3" style="display: none;">
                        <label class="form-label">Description (optional)</label>
                        <textarea name="description"
                                  class="form-control"
                                  maxlength="200"></textarea>
                    </div>

                    <div class="text-end">
                        <button class="btn btn-primary">
                            Save Public Holiday
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ================= TAB 2: UPLOAD EXCEL ================= -->
    <div class="tab-pane fade"
         id="upload"
         role="tabpanel"
         aria-labelledby="upload-tab">

        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                Upload Public Holidays
            </div>

            <div class="card-body">
                <form action="{{ route('v1.leave-application.import') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <!-- Year -->
                    <div class="mb-3">
                        <label class="form-label">Select Year</label>
                        <select id="year"name="year" class="form-select" required>
                            <option value="">-- Select Year --</option>
                            @for ($y = now()->year; $y <= now()->year + 2; $y++)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Country -->
                    <div class="mb-3">
                        <label class="form-label">Select Country</label>
                        <select id="country"name="country" class="form-select" required>
                            <option value="">-- Select Country --</option>
                            <option value="SG">Singapore</option>
                            <option value="MY">Malaysia</option>
                        </select>
                    </div>

                    <!-- Download Template -->
                    <div class="mb-3">
                        <a href="#"
                        id="downloadTemplateBtn"
                           class="btn btn-outline-primary">
                            <i class="bi bi-download"></i>
                            Download Excel Template
                        </a>
                    </div>
                    <script>
document.getElementById('downloadTemplateBtn').addEventListener('click', function (e) {
    e.preventDefault();

    const year = document.getElementById('year').value;
    const country = document.getElementById('country').value;

    if (!year && !country) {
        alert('Please select Year and Country first.');
        return;
    }

    const url = `{{ route('v1.leave-application.template-download') }}?year=${year}&country=${country}`;
    window.location.href = url;
});
</script>


                    <!-- Upload File -->
                    <div class="mb-4">
                        <label class="form-label">Upload Excel File</label>
                        <input type="file"
                               name="file"
                               class="form-control"
                               accept=".csv"
                               required>
                    </div>

                    <div class="text-end">
                        <button class="btn btn-success">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

        </div>
    </div>
@endsection
