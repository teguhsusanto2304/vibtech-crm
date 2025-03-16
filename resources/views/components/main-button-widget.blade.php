<div class="col-12 col-md-4">
    <div class="card text-center">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title">Create New</h5>
            <p class="card-text">Create a New {{ $title }}</p>
            @can('create-job-requisition')
                <a href="{{ route($routeName) }}" class="btn btn_primary mt-auto">
                    Create New
                </a>
            @else
                <a href="#" class="btn btn_primary mt-auto">You Can't Create New</a>
            @endcan
        </div>
    </div>
</div>
