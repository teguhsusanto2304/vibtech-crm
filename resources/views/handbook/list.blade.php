@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                @foreach ($breadcrumb as $item)
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0);">{{ $item }}</a>
                        <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                    </li>
                @endforeach
            </ol>
        </nav>

        <h3>{{ $title }}</h3>
        @if($selectedPost && $selectedPost->path_file)
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const pdfModal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
                const triggerBtn = document.createElement('button');
                triggerBtn.setAttribute('data-pdf-url', "{{ asset($selectedPost->path_file) }}");

                // Attach it manually as if it's the relatedTarget
                const event = new Event('show.bs.modal', { bubbles: true });
                event.relatedTarget = triggerBtn;

                // Trigger modal manually with correct context
                document.getElementById('pdfPreviewModal').dispatchEvent(event);
                pdfModal.show();
            });
        </script>
        @endif

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- DataTable Dependencies -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>

    <!-- Card -->
    <div class="card">
        <div class="card-header text-white d-flex flex-wrap justify-content-between align-items-center">
            <div>  </div>
            @can('create-employee-handbook')
            <a href="{{ route('v1.employee-handbooks.create')}}" class="btn btn-primary">Create New Employee Handbook</a>
            @endcan
            <!-- Department Filter Box -->
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($posts as $post)
                    @if((auth()->user()->can('view-employee-handbook') && $post->data_status==0) || $post->data_status==1 )
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 d-flex flex-column align-items-center text-center" style="position: relative;">
                            @can('create-employee-handbook')
                            <div style="
                                position: absolute;
                                top: 10px;
                                left: -10px;
                                background: {{ $post->data_status == 1 ? '#28a745' : '#ffc107' }}; /* Green for published, yellow for draft */
                                color: white;
                                padding: 2px 10px;
                                transform: rotate(-0deg);
                                font-weight: bold;
                                font-size: 11px;
                                box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                            ">
                                {{ $post->data_status == 1 ? 'Active' : 'Archive' }}
                            </div>
                        @endcan
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $post->title }}</h5>
                                <p class="card-text">{{ Str::limit($post->description, 100) }}</p>
                                <p><svg
                                    style="cursor: pointer;"
                                    data-bs-toggle="modal"
                                            data-bs-target="#pdfPreviewModal"
                                            data-pdf-url="{{ $post->path_file ? asset($post->path_file):'' }}"

                                    height="80px" width="80px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    viewBox="0 0 512 512" xml:space="preserve">
                               <path style="fill:#E2E5E7;" d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"/>
                               <path style="fill:#B0B7BD;" d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"/>
                               <polygon style="fill:#CAD1D8;" points="480,224 384,128 480,128 "/>
                               <path style="fill:#F15642;" d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16
                                   V416z"/>
                               <g>
                                   <path style="fill:#FFFFFF;" d="M101.744,303.152c0-4.224,3.328-8.832,8.688-8.832h29.552c16.64,0,31.616,11.136,31.616,32.48
                                       c0,20.224-14.976,31.488-31.616,31.488h-21.36v16.896c0,5.632-3.584,8.816-8.192,8.816c-4.224,0-8.688-3.184-8.688-8.816V303.152z
                                        M118.624,310.432v31.872h21.36c8.576,0,15.36-7.568,15.36-15.504c0-8.944-6.784-16.368-15.36-16.368H118.624z"/>
                                   <path style="fill:#FFFFFF;" d="M196.656,384c-4.224,0-8.832-2.304-8.832-7.92v-72.672c0-4.592,4.608-7.936,8.832-7.936h29.296
                                       c58.464,0,57.184,88.528,1.152,88.528H196.656z M204.72,311.088V368.4h21.232c34.544,0,36.08-57.312,0-57.312H204.72z"/>
                                   <path style="fill:#FFFFFF;" d="M303.872,312.112v20.336h32.624c4.608,0,9.216,4.608,9.216,9.072c0,4.224-4.608,7.68-9.216,7.68
                                       h-32.624v26.864c0,4.48-3.184,7.92-7.664,7.92c-5.632,0-9.072-3.44-9.072-7.92v-72.672c0-4.592,3.456-7.936,9.072-7.936h44.912
                                       c5.632,0,8.96,3.344,8.96,7.936c0,4.096-3.328,8.704-8.96,8.704h-37.248V312.112z"/>
                               </g>
                               <path style="fill:#CAD1D8;" d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"/>
                               </svg></p>
                                <div class="mt-auto d-flex justify-content-center">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a href="{{ route('v1.getting-started.read',['id'=>$post->id]) }}" class="btn btn-primary btn-sm" title="read employee handbook" data-bs-toggle="modal"
                                            data-bs-target="#pdfPreviewModal"
                                            data-pdf-url="{{ $post->path_file ? asset($post->path_file):'' }}"><svg fill="#ffff" width="20px" height="20px" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 28.0118 11.3945 C 26.2071 9.1445 21.7774 7.1523 16.8555 7.1523 C 10.3399 7.1523 5.0899 10.5039 3.8008 13.6445 L 3.8008 46.3867 C 3.8008 48.1680 4.9727 48.8477 6.2383 48.8477 C 7.2696 48.8477 7.8555 48.5430 8.4883 48.0508 C 9.8243 46.9023 12.5196 45.3555 16.8555 45.3555 C 21.1680 45.3555 24.1914 46.8555 25.3633 47.9102 C 25.9727 48.4023 26.7227 48.8477 28.0118 48.8477 C 29.2774 48.8477 30.0040 48.3555 30.6368 47.9102 C 31.8790 46.9258 34.8321 45.3555 39.1446 45.3555 C 43.4805 45.3555 46.1992 46.9258 47.5120 48.0508 C 48.1446 48.5430 48.7307 48.8477 49.7617 48.8477 C 51.0275 48.8477 52.1992 48.1680 52.1992 46.3867 L 52.1992 13.6445 C 50.9104 10.5039 45.6602 7.1523 39.1446 7.1523 C 34.2227 7.1523 29.7930 9.1445 28.0118 11.3945 Z M 7.5743 14.7695 C 8.1133 13.3164 11.5118 10.6914 16.8555 10.6914 C 22.1758 10.6914 25.6446 13.3398 26.1133 14.7695 L 26.1133 44.4180 C 23.8868 42.7539 20.4883 41.8164 16.8555 41.8164 C 13.1992 41.8164 9.8008 42.7539 7.5743 44.5117 Z M 48.4259 14.7695 L 48.4259 44.5117 C 46.1992 42.7539 42.8008 41.8164 39.1446 41.8164 C 35.5118 41.8164 32.1133 42.7539 29.8868 44.4180 L 29.8868 14.7695 C 30.3555 13.3398 33.8243 10.6914 39.1446 10.6914 C 44.4883 10.6914 47.8868 13.3164 48.4259 14.7695 Z"/></svg></a>

                                        <a href="{{ $post->path_file ? asset($post->path_file):'' }}" download class="btn btn-secondary btn-sm" title="download employee handbook"><svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 16L12 8" stroke="#ffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M9 13L11.913 15.913V15.913C11.961 15.961 12.039 15.961 12.087 15.913V15.913L15 13" stroke="#ffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M3 15L3 16L3 19C3 20.1046 3.89543 21 5 21L19 21C20.1046 21 21 20.1046 21 19L21 16L21 15" stroke="#ffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg></a>

                                        @if($post->data_status==1)
                                            @can('edit-getting-started')
                                                <a href="{{ route('v1.employee-handbooks.edit',['id'=>$post->id]) }}" class="btn btn-info btn-sm" title="edit employee handbook"><svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z" stroke="#ffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="#ffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg></a>
                                            @endcan
                                            @can('destroy-getting-started')
                                            <form class="row g-3"
                                            id="deleteForm{{ $post->id }}"
                                            action="{{ route('v1.employee-handbooks.destroy', ['id'=>$post->id,'status'=>0]) }}"
                                            method="post">
                                            @csrf
                                            <button type="button" class="btn btn-warning  btn-sm" title="delete employee handbook" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal{{ $post->id }}"><svg fill="#ffff" height="20px" width="20px" version="1.1" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns"
                                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"  viewBox="0 0 24 24"
                                                xml:space="preserve">
                                           <g id="inactive">
                                               <path d="M13.6,23.9c-7.8,1-14.5-5.6-13.5-13.5c0.7-5.3,5-9.7,10.3-10.3c7.8-1,14.5,5.6,13.5,13.5C23.2,18.9,18.9,23.2,13.6,23.9z
                                                    M13.7,2.1C6.9,1,1,6.9,2.1,13.7c0.7,4.1,4,7.5,8.2,8.2C17.1,23,23,17.1,21.9,10.3C21.2,6.2,17.8,2.8,13.7,2.1z"/>
                                               <polyline points="5.6,4.2 19.8,18.3 18.4,19.8 4.2,5.6 	"/>
                                           </g>
                                           </svg></button>
                                            @method('PUT')
                                                </form>
                                                <!-- Modal -->
<div class="modal fade" id="confirmDeleteModal{{ $post->id }}" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Archive</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to archive this employee handbook?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-warning" onclick="document.getElementById('deleteForm{{ $post->id }}').submit();">Yes, Archive it</button>
        </div>
      </div>
    </div>
  </div>
                                            @endcan
                                    @endif
                                    @if($post->data_status==0)
                                    @can('destroy-getting-started')
                                    <form class="row g-3"
                                    id="restoreForm{{ $post->id }}"
                                    action="{{ route('v1.employee-handbooks.destroy', ['id'=>$post->id,'status'=>1]) }}"
                                    method="post">
                                    @csrf
                                    <button type="button" class="btn btn-success  btn-sm" title="restore employee handbook" data-bs-toggle="modal" data-bs-target="#confirmRestoreModal{{ $post->id }}"><svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.52185 7H7C7.55229 7 8 7.44772 8 8C8 8.55229 7.55228 9 7 9H3C1.89543 9 1 8.10457 1 7V3C1 2.44772 1.44772 2 2 2C2.55228 2 3 2.44772 3 3V5.6754C4.26953 3.8688 6.06062 2.47676 8.14852 1.69631C10.6633 0.756291 13.435 0.768419 15.9415 1.73041C18.448 2.69239 20.5161 4.53782 21.7562 6.91897C22.9963 9.30013 23.3228 12.0526 22.6741 14.6578C22.0254 17.263 20.4464 19.541 18.2345 21.0626C16.0226 22.5842 13.3306 23.2444 10.6657 22.9188C8.00083 22.5931 5.54702 21.3041 3.76664 19.2946C2.20818 17.5356 1.25993 15.3309 1.04625 13.0078C0.995657 12.4579 1.45216 12.0088 2.00445 12.0084C2.55673 12.0079 3.00351 12.4566 3.06526 13.0055C3.27138 14.8374 4.03712 16.5706 5.27027 17.9625C6.7255 19.605 8.73118 20.6586 10.9094 20.9247C13.0876 21.1909 15.288 20.6513 17.0959 19.4075C18.9039 18.1638 20.1945 16.3018 20.7247 14.1724C21.2549 12.043 20.9881 9.79319 19.9745 7.8469C18.9608 5.90061 17.2704 4.3922 15.2217 3.6059C13.173 2.8196 10.9074 2.80968 8.8519 3.57803C7.11008 4.22911 5.62099 5.40094 4.57993 6.92229C4.56156 6.94914 4.54217 6.97505 4.52185 7Z" fill="#ffff"/>
                                        </svg></button>
                                    @method('PUT')
                                        </form>
<!-- Modal -->
<div class="modal fade" id="confirmRestoreModal{{ $post->id }}" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Restore</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to restore this employee handbook archives?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success" onclick="document.getElementById('restoreForm{{ $post->id }}').submit();">Yes, Restore it</button>
        </div>
      </div>
    </div>
  </div>

                                    <form class="row g-3"
                                    id="permanentDeleteForm{{ $post->id }}"
                                    action="{{ route('v1.employee-handbooks.destroy', ['id'=>$post->id,'status'=>3]) }}"
                                    method="post">
                                    @csrf
                                    <button type="button" class="btn btn-danger  btn-sm" title="delete employee handbook" data-bs-toggle="modal" data-bs-target="#confirmPermanentDeleteModal{{ $post->id }}"><svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6M14 10V17M10 10V17" stroke="#ffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg></button>
                                    @method('PUT')
                                        </form>
<!-- Modal -->
<div class="modal fade" id="confirmPermanentDeleteModal{{ $post->id }}" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Permanent Delete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to permanent delete this employee handbook archives?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" onclick="document.getElementById('permanentDeleteForm{{ $post->id }}').submit();">Yes, Delete it</button>
        </div>
      </div>
    </div>
  </div>

                                    @endcan
                                    @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                <!-- $post->created_at->diffForHumans() -->
                                <small>{{ $post->created_at->format('d M Y') }} | {{ $post->user->name }}</small>
                            </div>
                        </div>
                    </div>
                    @endif
                @empty
                    <p>No posts available.</p>
                @endforelse
            </div>

            <div class="d-flex justify-content-center">
                {!! $posts->links() !!}
            </div>
        </div>
    </div>
<!-- Modal -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">PDF Preview</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-0">
          <!-- Container for either the PDF or the "not found" message -->
          <div id="pdfContainer" style="width:100%; height:80vh; position:relative;">
            <!-- We'll inject either an <object> or a fallback div here -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    var pdfModal = document.getElementById('pdfPreviewModal');
    pdfModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      var pdfUrl = button.getAttribute('data-pdf-url');
      var container = document.getElementById('pdfContainer');
      container.innerHTML = ''; // clear previous contents

      if (pdfUrl) {
      // Create an <object> to embed the PDF
      var obj = document.createElement('object');
      obj.data = pdfUrl;
      obj.type = 'application/pdf';
      obj.width = '100%';
      obj.height = '100%';
      // If loading fails, the browser will fire the 'error' event


      container.appendChild(obj);
      } else {
        container.innerHTML =
          '<div style="display:flex;align-items:center;justify-content:center; height:100%; color:#888;">' +
            '🚫 File not found.' +
          '</div>';
      }
    });

    // Clear out on hide
    pdfModal.addEventListener('hidden.bs.modal', function () {
      document.getElementById('pdfContainer').innerHTML = '';
    });
  </script>


@endsection
