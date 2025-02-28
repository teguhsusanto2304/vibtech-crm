@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
    <!-- custom-icon Breadcrumb-->
    <nav aria-label="breadcrumb">

        <ol class="breadcrumb breadcrumb-custom-icon">
            @foreach ($breadcrumb as $item )
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">{{ $item }}</a>
                <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
            </li>
            @endforeach
        </ol>
    </nav>

    <h2>Employee handbook</h2>
    <style>

        .card {
          background-color: #004080; /* Dark blue background */
          color: white;
          padding: 10px;
          border-radius: 10px;
          width: 250px; /* Adjust width as needed */
          box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
          align-items: center;
        }


        .pdf-icon {
          width: 100px;
          height: 100px;
          margin: 0 auto;
          display: block;
        }

        .pdf-icon path {
          fill: white;
        }




      </style>
      <div class="card">
        <p ><strong>Vibtech Genesis</strong></p>
        <p><strong>Employee Handbook</strong></p>

        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="100" height="100" viewBox="0 0 256 256" xml:space="preserve">

            <defs>
            </defs>
            <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                <path d="M 81.289 17.28 L 65.341 1.332 C 64.482 0.473 63.34 0 62.125 0 H 19.557 c -2.508 0 -4.548 2.04 -4.548 4.548 V 43.16 h 1.5 v 30.786 v 0 h -1.5 v 11.506 c 0 2.508 2.04 4.548 4.548 4.548 h 58.517 c 2.508 0 4.548 -2.04 4.548 -4.548 V 20.496 C 82.621 19.281 82.148 18.14 81.289 17.28 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(220,223,225); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                <path d="M 65.544 45.16 H 15.009 v 30.786 h 50.536 c 1.534 0 2.777 -1.199 2.777 -2.677 V 47.837 C 68.321 46.359 67.078 45.16 65.544 45.16 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(196,203,210); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                <path d="M 7.379 71.269 c 0 1.479 1.243 2.677 2.777 2.677 h 53.317 c 1.534 0 2.777 -1.199 2.777 -2.677 V 45.837 c 0 -1.479 -1.243 -2.677 -2.777 -2.677 H 10.156 c -1.534 0 -2.777 1.199 -2.777 2.677 V 71.269 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(234,84,64); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                <path d="M 22.931 48.752 h -5.944 c -0.829 0 -1.5 0.672 -1.5 1.5 v 9.854 v 6.747 c 0 0.828 0.671 1.5 1.5 1.5 s 1.5 -0.672 1.5 -1.5 v -5.247 h 4.444 c 2.392 0 4.338 -1.946 4.338 -4.339 v -4.177 C 27.269 50.698 25.323 48.752 22.931 48.752 z M 24.269 57.268 c 0 0.738 -0.601 1.339 -1.338 1.339 h -4.444 v -6.854 h 4.444 c 0.738 0 1.338 0.601 1.338 1.339 V 57.268 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                <path d="M 37.924 68.354 h -5.652 c -0.829 0 -1.5 -0.672 -1.5 -1.5 V 50.252 c 0 -0.828 0.671 -1.5 1.5 -1.5 h 5.652 c 2.553 0 4.63 2.077 4.63 4.631 v 10.341 C 42.555 66.276 40.477 68.354 37.924 68.354 z M 33.772 65.354 h 4.152 c 0.899 0 1.63 -0.731 1.63 -1.63 V 53.383 c 0 -0.899 -0.731 -1.631 -1.63 -1.631 h -4.152 V 65.354 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                <path d="M 56.643 48.752 H 47.86 c -0.828 0 -1.5 0.672 -1.5 1.5 v 16.602 c 0 0.828 0.672 1.5 1.5 1.5 s 1.5 -0.672 1.5 -1.5 v -6.801 h 4.252 c 0.828 0 1.5 -0.672 1.5 -1.5 s -0.672 -1.5 -1.5 -1.5 H 49.36 v -5.301 h 7.282 c 0.828 0 1.5 -0.672 1.5 -1.5 S 57.471 48.752 56.643 48.752 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                <polygon points="64.08,18.41 82.62,36.88 82.55,19.73 " style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(196,203,210); fill-rule: nonzero; opacity: 1;" transform="  matrix(1 0 0 1 0 0) "/>
                <path d="M 81.289 17.28 L 65.341 1.332 c -0.675 -0.676 -1.529 -1.102 -2.453 -1.258 v 15.382 c 0 2.358 1.919 4.277 4.277 4.277 h 15.382 C 82.391 18.81 81.965 17.956 81.289 17.28 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(171,178,184); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
            </g>
            </svg>


            <style>
                .link {
                  color: #fff;
                  font-weight: regular;
                  font-size: 12px;
                  text-decoration: underline; /* Add this line for underline */
                }
              </style>
                <br>
                <div class="row text-center" >
                <div class="col-5 text-center" >
                    <a href="#" class="link" >Read Online</a>
                </div>
                <div class="col-7 text-center">
                    <a href="#" class="link">Download A Copy</a>
                </div>
                <p class="invisible">transparan</p>
            </div>
      </div>
    </div>
</div>
@endsection
