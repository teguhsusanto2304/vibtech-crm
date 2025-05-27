{{-- resources/views/components/filter-form.blade.php --}}

@props(['salesPersons', 'industries', 'countries', 'formId' => 'filters-form'])

<div id="{{ $formId }}" class="row g-3">
    @if($salesPersons)
        <div class="col-md-4">
            <label for="filter-sales-person" class="form-label">Sales Person</label>
            <select id="filter-sales-person" class="form-select">
                <option value="">All Sales Persons</option>
                <option value="-">Unassigned Sales Person</option>
                @foreach ($salesPersons as $salesPerson)
                    <option value="{{ $salesPerson->name }}">{{ $salesPerson->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="col-md-4">
        <label for="filter-industry" class="form-label">Industry</label>
        <select id="filter-industry" class="form-select">
            <option value="">All Industries</option>
            @foreach ($industries as $industry)
                <option value="{{ $industry->name }}">{{ $industry->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label for="filter-country" class="form-label">Country</label>
        <select id="filter-country" class="form-select">
            <option value="">All Countries</option>
            @foreach ($countries as $country)
                <option value="{{ $country->name }}">{{ $country->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-8 d-flex align-items-end justify-content-start mt-4">
        <div class="btn-group" role="group">
            <button id="download-csv" class="btn btn-outline-primary">Download CSV</button>
            <button id="download-pdf" class="btn btn-outline-danger">Download PDF</button>
            <button type="button" id="reset-filters" class="btn btn-secondary">Reset Filters</button>
        </div>
    </div>
</div>
