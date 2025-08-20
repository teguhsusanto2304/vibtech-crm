{{-- _modal_content.blade.php --}}

@php
    // Ensure $phase and $mode (from controller) are available
    $readOnly = ($mode === 'view');
@endphp

<form id="editStageForm" data-project-id="{{ $project->obfuscated_id ?? $project->id }}" data-phase-id="{{ $stage->id ?? $stage->id }}">
    @csrf
    {{-- Laravel's method spoofing for PUT/PATCH requests when using POST --}}
    {{-- If your form submit method is set to 'POST' in AJAX, you'll need this --}}
    {{-- <input type="hidden" name="_method" value="PUT"> --}}

    <div class="mb-3">
        <label for="phaseName" class="form-label">Stage Name</label>
        @if($readOnly)
            <p class="form-control-plaintext">{{ $stage->name }}</p>
        @else
            <input type="text" class="form-control" id="stageName" name="name" value="{{ old('name', $stage->name) }}" {{ $readOnly ? 'readonly' : '' }} required>
        @endif
    </div>
</form>