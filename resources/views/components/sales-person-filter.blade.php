{{-- resources/views/components/filter-form.blade.php --}}

@props(['salesPersons', 'formId' => 'filters-form','salesPersonTitle'=>'Sales Person'])

<div id="{{ $formId }}" class="row g-3">
    @if($salesPersons)
        <div class="col-12">
            <label for="filter-sales-person" class="form-label">{{ $salesPersonTitle }}</label>
            <select id="filter-sales-person" class="form-select">
                <option value="">
                    @if($salesPersonTitle=='Recommended by')
                     All Sales Persons
                    @else
                     All Persons
                    @endif</option>
                @foreach ($salesPersons as $salesPerson)
                    <option value="{{ $salesPerson->name }}">{{ $salesPerson->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
</div>
