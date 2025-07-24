{{-- _modal_content.blade.php --}}

@php
    // Ensure $phase and $mode (from controller) are available
    $readOnly = ($mode === 'view');
@endphp

<form id="editPhaseForm" data-project-id="{{ $project->obfuscated_id ?? $project->id }}" data-phase-id="{{ $phase->obfuscated_id ?? $phase->id }}">
    @csrf
    {{-- Laravel's method spoofing for PUT/PATCH requests when using POST --}}
    {{-- If your form submit method is set to 'POST' in AJAX, you'll need this --}}
    {{-- <input type="hidden" name="_method" value="PUT"> --}}

    <div class="mb-3">
        <label for="phaseName" class="form-label">Phase Name</label>
        @if($readOnly)
            <p class="form-control-plaintext">{{ $phase->name }}</p>
        @else
            <input type="text" class="form-control" id="phaseName" name="name" value="{{ old('name', $phase->name) }}" {{ $readOnly ? 'readonly' : '' }} required>
        @endif
    </div>

    <div class="mb-3">
        <label for="phaseDescription" class="form-label">Description</label>
        @if($readOnly)
            <p class="form-control-plaintext">{{ $phase->description ?? 'N/A' }}</p>
        @else
            <textarea class="form-control" id="phaseDescription" name="description" rows="3" {{ $readOnly ? 'readonly' : '' }}>{{ old('description', $phase->description) }}</textarea>
        @endif
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="phaseStartDate" class="form-label">Start Date</label>
            @if($readOnly)
                <p class="form-control-plaintext">{{ $phase->start_date ? $phase->start_date->format('Y-m-d') : 'N/A' }}</p>
            @else
                <input type="date" class="form-control" id="phaseStartDate" name="start_date" value="{{ old('start_date', $phase->start_date ? $phase->start_date->format('Y-m-d') : '') }}" {{ $readOnly ? 'readonly' : '' }}>
            @endif
        </div>
        <div class="col-md-6 mb-3">
            <label for="phaseEndDate" class="form-label">End Date</label>
            @if($readOnly)
                <p class="form-control-plaintext">{{ $phase->end_date ? $phase->end_date->format('Y-m-d') : 'N/A' }}</p>
            @else
                <input type="date" class="form-control" id="phaseEndDate" name="end_date" value="{{ old('end_date', $phase->end_date ? $phase->end_date->format('Y-m-d') : '') }}" {{ $readOnly ? 'readonly' : '' }}>
            @endif
        </div>
    </div>

    <div class="mb-3">
        <label for="phaseStatus" class="form-label">Status</label>
            <p class="form-control-plaintext">
                <span class="badge {{ $phase->phase_status_badge }} rounded-pill">{{ $phase->phase_status }}</span>
            </p>
       
    </div>
</form>