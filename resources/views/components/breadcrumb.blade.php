<nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon">
        @foreach ($breadcrumb as $item)
            <li class="breadcrumb-item">
                @if($item == 'Job Assignment Form')
                    <a href="{{ route('v1.job-assignment-form') }}">{{ $item }}</a>
                @else
                    <a href="javascript:void(0);">{{ $item }}</a>
                @endif
                <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
            </li>
        @endforeach
    </ol>
</nav>
<h3>{{ $title }}</h3>
