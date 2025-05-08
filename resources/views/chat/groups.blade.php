@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- custom-icon Breadcrumb-->
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

            <style>
                .user {
                    padding: 8px;
                    border-bottom: 1px solid #eee;
                    cursor: pointer;
                }

                .user:hover {
                    background: #f0f0f0;
                }

                .chat-container {
                    width: 100%;
                    background: white;
                    padding: 15px;
                    border-radius: 10px;
                    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                }

                .chat-box {
                    max-height: 300px;
                    overflow-y: auto;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 10px;
                    background: #fff;
                }

                .message {
                    display: flex;
                    margin-bottom: 10px;
                }

                .message .bubble {
                    padding: 10px;
                    border-radius: 10px;
                    max-width: 90%;
                    word-wrap: break-word;
                }

                .sent {
                    justify-content: flex-end;
                }

                .sent .bubble {
                    background: #007bff;
                    color: white;
                    border-top-right-radius: 0;
                }

                .received {
                    justify-content: flex-start;
                }

                .received .bubble {
                    background: #e4e6eb;
                    color: black;
                    border-top-left-radius: 0;
                }

                .input-container {
                    display: flex;
                    margin-top: 10px;
                }

                .input-container input {
                    flex: 1;
                    padding: 8px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                }

                .input-container button {
                    margin-left: 5px;
                    padding: 8px 12px;
                    border: none;
                    background: #007bff;
                    color: white;
                    border-radius: 5px;
                    cursor: pointer;
                }

                .input-container button:hover {
                    background: #0056b3;
                }

                .message-container {
                    display: flex;
                    flex-direction: column;
                    max-width: 80%;
                    position: relative;
                    padding: 5px 10px;
                }

                .message-bubble {
                    padding: 10px 15px;
                    border-radius: 20px;
                    word-wrap: break-word;
                    display: inline-block;
                    max-width: 100%;
                }

                .sent .message-bubble {
                    background-color: #007bff;
                    color: white;
                    border-top-right-radius: 0;
                    align-self: flex-end;
                }

                .received .message-bubble {
                    background-color: #e4e6eb;
                    color: black;
                    border-top-left-radius: 0;
                    align-self: flex-start;
                }

                .message-timestamp {
                    font-size: 12px;
                    color: gray;
                    margin-top: 3px;
                    text-align: right;
                }
            </style>
            <div class="container">
                <!-- Online Users Sidebar -->
                <div class="row">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-header text-bg-success">
                                <strong>Users</strong>
                            </div>
                            <div class="card-body">
                                <ul id="member-list" style="list-style: none; padding: 0;">
                                </ul>
                            </div>
                        </div>
                        <div class="card mt-4">
                            <div class="card-header text-bg-primary">
                                <strong>Active Chat Group</strong>
                            </div>
                            <div class="card-body">
                                @forelse($groups as $group)
                                    <div class="modal fade" id="inviteUserModal{{ $group->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="inviteUserModalLabel{{ $group->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Add User to {{ $group->name }}</h5>

                                                </div>
                                                <form action="{{ route('chat-groups.invite-users', $group->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <table class="table table-striped" id="staffTable{{ $group->id }}">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th>Name</th>
                                                                    <th>Position</th>
                                                                    <th style="align-content: center">Add All
                                                                        <input type="checkbox" class ="form-check-input" id="selectAllCheckbox{{ $group->id }}" style="margin-left: 8px;">
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="staffTableBody{{ $group->id }}">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Add Selected
                                                            Users</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            const groupId = {{ $group->id }};
                                            const selectAllCheckbox = document.getElementById(`selectAllCheckbox${groupId}`);

                                            selectAllCheckbox.addEventListener('change', function () {
                                                const checkboxes = document.querySelectorAll(`#staffTableBody${groupId} input[type="checkbox"]`);
                                                checkboxes.forEach(cb => cb.checked = this.checked);
                                            });
                                        });
                                    </script>
                                    <div class="card h-100 mb-5 group-card" data-group-id="{{ $group->id }}"
                                        data-group-name="{{ $group->name }}">
                                        <div class="card-body d-flex flex-column user">
                                            <strong class="card-title d-flex justify-content-between align-items-center">
                                                {{ $group->name }}
                                                @if(!empty($group->unread_count) && $group->unread_count > 0)
                                                    <span class="badge bg-danger ms-2">
                                                        {{ $group->unread_count }}
                                                    </span>
                                                @endif
                                            </strong>
                                            @if($group->is_creator == 1)
                                                <div class="mt-auto d-flex justify-content-center">
                                                    <div class="btn-group btn-group-sm" style="justify-content: center;"
                                                        role="group" aria-label="Basic example">
                                                        <button data-toggle="modal"
                                                            data-target="#editChatGroupModal{{ $group->id }}"
                                                            class="btn btn-primary btn-sm">
                                                            <svg width="17px" height="17px" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z"
                                                                    stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                                <path
                                                                    d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13"
                                                                    stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg></button>
                                                            <!-- Edit Group Modal -->
                                                            <div class="modal fade" id="editChatGroupModal{{ $group->id }}" tabindex="-1" role="dialog" aria-labelledby="editGroupLabel{{ $group->id }}" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <form action="{{ route('chat-groups.update', $group->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PUT')

                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="editGroupLabel{{ $group->id }}">Edit Chat Group</h5>

                                                                            </div>

                                                                            <div class="modal-body">
                                                                                <div class="form-group">
                                                                                    <label for="groupName{{ $group->id }}">Group Name</label>
                                                                                    <input
                                                                                        type="text"
                                                                                        class="form-control"
                                                                                        id="groupName{{ $group->id }}"
                                                                                        name="name"
                                                                                        value="{{ $group->name }}"
                                                                                        required>
                                                                                </div>
                                                                            </div>

                                                                            <div class="modal-footer">
                                                                                <button type="submit" class="btn btn-success">Save Changes</button>&nbsp;
                                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                        <a href="#"
                                                            class="btn btn-info btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#confirmDeleteModal{{ $group->id }}">
                                                            <svg width="17px" height="17px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M12.5303 17.5303C12.3897 17.671 12.1989 17.75 12 17.75C11.8011 17.75 11.6103 17.671 11.4697 17.5303L8.96967 15.0303C8.67678 14.7374 8.67678 14.2626 8.96967 13.9697C9.26256 13.6768 9.73744 13.6768 10.0303 13.9697L11.25 15.1893V11C11.25 10.5858 11.5858 10.25 12 10.25C12.4142 10.25 12.75 10.5858 12.75 11V15.1893L13.9697 13.9697C14.2626 13.6768 14.7374 13.6768 15.0303 13.9697C15.3232 14.2626 15.3232 14.7374 15.0303 15.0303L12.5303 17.5303Z" fill="#FFFFFF"/>
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0574 1.25H11.9426C9.63423 1.24999 7.82519 1.24998 6.41371 1.43975C4.96897 1.63399 3.82895 2.03933 2.93414 2.93414C2.03933 3.82895 1.63399 4.96897 1.43975 6.41371C1.24998 7.82519 1.24999 9.63422 1.25 11.9426V12H1.26092C1.25 12.5788 1.25 13.2299 1.25 13.9664V14.0336C1.25 15.4053 1.24999 16.4807 1.32061 17.3451C1.39252 18.2252 1.54138 18.9523 1.87671 19.6104C2.42799 20.6924 3.30762 21.572 4.38956 22.1233C5.04769 22.4586 5.7748 22.6075 6.65494 22.6794C7.51927 22.75 8.59469 22.75 9.96637 22.75H14.0336C15.4053 22.75 16.4807 22.75 17.3451 22.6794C18.2252 22.6075 18.9523 22.4586 19.6104 22.1233C20.6924 21.572 21.572 20.6924 22.1233 19.6104C22.4586 18.9523 22.6075 18.2252 22.6794 17.3451C22.75 16.4807 22.75 15.4053 22.75 14.0336V13.9664C22.75 13.2302 22.75 12.5787 22.7391 12H22.75V11.9426C22.75 9.63423 22.75 7.82519 22.5603 6.41371C22.366 4.96897 21.9607 3.82895 21.0659 2.93414C20.1711 2.03933 19.031 1.63399 17.5863 1.43975C16.1748 1.24998 14.3658 1.24999 12.0574 1.25ZM4.38956 5.87671C3.82626 6.16372 3.31781 6.53974 2.88197 6.98698C2.89537 6.85884 2.91012 6.73444 2.92637 6.61358C3.09825 5.33517 3.42514 4.56445 3.9948 3.9948C4.56445 3.42514 5.33517 3.09825 6.61358 2.92637C7.91356 2.75159 9.62177 2.75 12 2.75C14.3782 2.75 16.0864 2.75159 17.3864 2.92637C18.6648 3.09825 19.4355 3.42514 20.0052 3.9948C20.5749 4.56445 20.9018 5.33517 21.0736 6.61358C21.0899 6.73445 21.1046 6.85884 21.118 6.98698C20.6822 6.53975 20.1737 6.16372 19.6104 5.87671C18.9523 5.54138 18.2252 5.39252 17.3451 5.32061C16.4807 5.24999 15.4053 5.25 14.0336 5.25H9.96645C8.59472 5.25 7.51929 5.24999 6.65494 5.32061C5.7748 5.39252 5.04769 5.54138 4.38956 5.87671ZM5.07054 7.21322C5.48197 7.00359 5.9897 6.87996 6.77708 6.81563C7.57322 6.75058 8.58749 6.75 10 6.75H14C15.4125 6.75 16.4268 6.75058 17.2229 6.81563C18.0103 6.87996 18.518 7.00359 18.9295 7.21322C19.7291 7.62068 20.3793 8.27085 20.7868 9.07054C20.9964 9.48197 21.12 9.9897 21.1844 10.7771C21.2494 11.5732 21.25 12.5875 21.25 14C21.25 15.4125 21.2494 16.4268 21.1844 17.2229C21.12 18.0103 20.9964 18.518 20.7868 18.9295C20.3793 19.7291 19.7291 20.3793 18.9295 20.7868C18.518 20.9964 18.0103 21.12 17.2229 21.1844C16.4268 21.2494 15.4125 21.25 14 21.25H10C8.58749 21.25 7.57322 21.2494 6.77708 21.1844C5.9897 21.12 5.48197 20.9964 5.07054 20.7868C4.27085 20.3793 3.62068 19.7291 3.21322 18.9295C3.00359 18.518 2.87996 18.0103 2.81563 17.2229C2.75058 16.4268 2.75 15.4125 2.75 14C2.75 12.5875 2.75058 11.5732 2.81563 10.7771C2.87996 9.9897 3.00359 9.48197 3.21322 9.07054C3.62068 8.27085 4.27085 7.62069 5.07054 7.21322Z" fill="#FFFFFF"/>
                                                                </svg>
                                                        </a>
                                                        <!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal{{ $group->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel{{ $group->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form method="POST" action="{{ route('chat-groups.destroy', ['id'=> $group->id,'type'=>'archive']) }}">
          @csrf
          @method('PUT') <!-- Or use PUT if you're "soft deleting" or marking status -->
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="confirmDeleteLabel{{ $group->id }}">Confirm Deletion</h5>

            </div>
            <div class="modal-body">
              Are you sure you want to archive chats on <strong>{{ $group->name }}</strong>?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>&nbsp;
              <button type="submit" class="btn btn-danger">Yes, Archive</button>
            </div>
          </div>
      </form>
    </div>
  </div>

  <a href="#"
                                                            class="btn btn-danger btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#confirmPermanentDeleteModal{{ $group->id }}">
                                                            <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6M14 10V17M10 10V17"
                                                                    stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg>
                                                        </a>
                                                        <!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmPermanentDeleteModal{{ $group->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel{{ $group->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form method="POST" action="{{ route('chat-groups.destroy', ['id'=> $group->id,'type'=>'delete']) }}">
          @csrf
          @method('PUT') <!-- Or use PUT if you're "soft deleting" or marking status -->
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="confirmDeleteLabel{{ $group->id }}">Confirm Deletion</h5>

            </div>
            <div class="modal-body">
              Are you sure you want to delete <strong>{{ $group->name }}</strong>?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>&nbsp;
              <button type="submit" class="btn btn-danger">Yes, Delete</button>
            </div>
          </div>
      </form>
    </div>
  </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td>No chat group available.</td>
                                    </tr>
                                @endforelse
                                <div class="card h-100 mb-5 group-card">
                                    <div class="card-body d-flex flex-column user">
                                        <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#createChatGroupModal">
                                            Create a Chat Group
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-4">
                            <div class="card-header text-bg-info">
                                <strong>Archive Chat Group</strong>
                            </div>
                            <div class="card-body">
                                @forelse($del_groups as $del_group)
                                <div class="card h-100 mb-5 group-card" data-group-id="{{ $del_group->id }}"
                                    data-group-name="{{ $del_group->name }}" data-group-status="0">
                                    <div class="card-body d-flex flex-column user">
                                        <strong class="card-title">{{ $del_group->name }}</strong>
                                        @if($del_group->is_creator == 1)
                                                <div class="mt-auto d-flex justify-content-center">
                                                    <div class="btn-group btn-group-sm" style="justify-content: center;"
                                                        role="group" aria-label="Basic example">
                                                        <a href="#"
                                                            class="btn btn-success btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#confirmRestoreModal{{ $del_group->id }}">
                                                            <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M4.52185 7H7C7.55229 7 8 7.44772 8 8C8 8.55229 7.55228 9 7 9H3C1.89543 9 1 8.10457 1 7V3C1 2.44772 1.44772 2 2 2C2.55228 2 3 2.44772 3 3V5.6754C4.26953 3.8688 6.06062 2.47676 8.14852 1.69631C10.6633 0.756291 13.435 0.768419 15.9415 1.73041C18.448 2.69239 20.5161 4.53782 21.7562 6.91897C22.9963 9.30013 23.3228 12.0526 22.6741 14.6578C22.0254 17.263 20.4464 19.541 18.2345 21.0626C16.0226 22.5842 13.3306 23.2444 10.6657 22.9188C8.00083 22.5931 5.54702 21.3041 3.76664 19.2946C2.20818 17.5356 1.25993 15.3309 1.04625 13.0078C0.995657 12.4579 1.45216 12.0088 2.00445 12.0084C2.55673 12.0079 3.00351 12.4566 3.06526 13.0055C3.27138 14.8374 4.03712 16.5706 5.27027 17.9625C6.7255 19.605 8.73118 20.6586 10.9094 20.9247C13.0876 21.1909 15.288 20.6513 17.0959 19.4075C18.9039 18.1638 20.1945 16.3018 20.7247 14.1724C21.2549 12.043 20.9881 9.79319 19.9745 7.8469C18.9608 5.90061 17.2704 4.3922 15.2217 3.6059C13.173 2.8196 10.9074 2.80968 8.8519 3.57803C7.11008 4.22911 5.62099 5.40094 4.57993 6.92229C4.56156 6.94914 4.54217 6.97505 4.52185 7Z" fill="#FFFFFF"/>
                                                            </svg>
                                                        </a>
                                                        <!-- Confirm Delete Modal -->
                                                        <div class="modal fade" id="confirmRestoreModal{{ $del_group->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel{{ $del_group->id }}" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                            <form method="POST" action="{{ route('chat-groups.destroy', ['id'=> $del_group->id,'type'=>'restore']) }}">
                                                                @csrf
                                                                @method('PUT') <!-- Or use PUT if you're "soft deleting" or marking status -->
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                    <h5 class="modal-title" id="confirmDeleteLabel{{ $del_group->id }}">Confirm Restore</h5>

                                                                    </div>
                                                                    <div class="modal-body">
                                                                    Are you sure you want to restore <strong>{{ $del_group->name }}</strong>?
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>&nbsp;
                                                                    <button type="submit" class="btn btn-success">Yes, Restore</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            </div>
                                                        </div>

                                                        <a href="#"
                                                            class="btn btn-danger btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#confirmPermanentDeleteModal{{ $del_group->id }}">
                                                            <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6M14 10V17M10 10V17"
                                                                    stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg>
                                                        </a>
                                                        <!-- Confirm Delete Modal -->
                                                        <div class="modal fade" id="confirmPermanentDeleteModal{{ $del_group->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel{{ $del_group->id }}" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                            <form method="POST" action="{{ route('chat-groups.destroy', ['id'=> $del_group->id,'type'=>'delete']) }}">
                                                                @csrf
                                                                @method('PUT') <!-- Or use PUT if you're "soft deleting" or marking status -->
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                    <h5 class="modal-title" id="confirmDeleteLabel{{ $del_group->id }}">Confirm Deletion</h5>

                                                                    </div>
                                                                    <div class="modal-body">
                                                                    Are you sure you want to delete <strong>{{ $del_group->name }}</strong>?
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>&nbsp;
                                                                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                    </div>
                                </div>
                                @empty
                                    <tr>
                                        <td>No chat group available.</td>
                                    </tr>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="card">
                            <div class="card-header text-bg-light">
                                <div class="invisible" style="display: none;">
                                    <label for="recipient">To:</label>
                                    <input type="text" id="recipient" placeholder="Enter recipient ID">
                                    <button onclick="loadPreviousMessages()">Load Chat</button>
                                </div>
                                <strong id="recipientLabel"></strong>
                            </div>
                            <div class="card-body mt-4">
                                <div class="chat-box" id="chat-box"></div>
                                <div class="input-container">
                                    <input type="text" id="message-input" placeholder="Type a message">
                                    <input type="hidden" id="group-id" >
                                    <button class="btn btn-primary" id="send-button">Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
<div class="modal fade" id="createChatGroupModal" tabindex="-1" role="dialog" aria-labelledby="createChatGroupLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('chat-groups') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createChatGroupLabel">Create Chat Group</h5>

                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="group-name">Group Name</label>
                        <input type="text" name="name" id="group-name" class="form-control" placeholder="Enter group name" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Create</button>&nbsp;
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
                    @php
                    $users = \App\Models\User::all();  // Or use a more specific query, like ->select('username', 'avatar')

                    // Format the users as needed (optional)
                    $formattedUsers = $users->map(function($user) {
                        return [
                            'username' => $user->name,
                            'avatar' => $user->path_image ? asset($user->path_image) : null,  // Assuming avatar is stored as a filename
                        ];
                    });
                    @endphp

                <script>
                    const usersAvatar = @json($formattedUsers);
                    document.addEventListener("DOMContentLoaded", function () {
                            const messageInput = document.getElementById("message-input");

                            messageInput.addEventListener("keydown", function (event) {
                                if (event.key === "Enter" && !event.shiftKey) {

                                    event.preventDefault(); // Prevent newline or form submission
                                    const message = document.getElementById("message-input").value;
                                    const groupId = document.getElementById("group-id").value;

                                    if (message.trim() === "") return;

                                    $.ajax({
                                        url: "{{ route('chat-group.send-message') }}",
                                        method: "POST",
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            message: message,
                                            group_id: groupId
                                        },
                                        success: function (response) {
                                            $('#message-input').val('');
                                            addMessage(response.sender_name, response.message, response.timestamp);
                                        },
                                        error: function () {
                                            alert("❌ Failed to send message.");
                                        }
                                    });
                                }
                            });
                        });
                    $(document).ready(function () {
                        $('#send-button').click(function () {
                            let message = $('#message-input').val();
                            let groupId = $('#group-id').val(); // Pass this from the Blade context

                            if (message.trim() === "") return;

                            $.ajax({
                                url: "{{ route('chat-group.send-message') }}",
                                method: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    message: message,
                                    group_id: groupId
                                },
                                success: function (response) {
                                    $('#message-input').val('');
                                    addMessage(response.sender_name, response.message, response.timestamp);
                                },
                                error: function () {
                                    alert("❌ Failed to send message.");
                                }
                            });
                        });
                    });
                    async function fetchInvitedUsers(groupId, tableBodyId) {
                        try {
                            const response = await fetch(`/chat-groups/${groupId}/invited-users`);
                            const data = await response.json();
                            updateInviteModal(data.users, data.invited_users, tableBodyId);
                        } catch (error) {
                            console.error("Error fetching invited users:", error);
                        }
                    }

                    function updateInviteModal(users, invitedUsers, tableBodyId) {
                        let tableBody = document.getElementById(tableBodyId);
                        tableBody.innerHTML = "";

                        users.forEach((user, index) => {
                            let isChecked = invitedUsers.includes(user.id) ? "checked" : "";
                            let avatarUrl = user.path_image ? user.path_image : '{{ asset("assets/img/photos/default.png") }}'; // fallback image
                            let row = `
                            <tr>
                                <td><img src="${avatarUrl}" alt="User Image" width="50" height="50" class="rounded-circle"></td>
                                <td>${user.name}</td>
                                <td>${user.position || "N/A"}</td>
                                <td>
                                    <input type="checkbox" class="form-check-input" name="invited_users[]" value="${user.id}" ${isChecked}>
                                </td>
                            </tr>
                        `;
                            tableBody.innerHTML += row;
                        });
                    }

                    document.addEventListener("DOMContentLoaded", function () {
                        document.getElementById("member-list").addEventListener("click", function (e) {
                            if (e.target && e.target.classList.contains("invite-user-btn")) {
                                const groupId = e.target.dataset.groupId;
                                const modalId = `inviteUserModal${groupId}`;
                                const tableBodyId = `staffTableBody${groupId}`;

                                const modalElement = document.getElementById(modalId);
                                const tableBody = document.getElementById(tableBodyId);

                                if (!modalElement || !tableBody) {
                                    console.error("❌ Modal or Table Body not found!");
                                    return;
                                }

                                fetchInvitedUsers(groupId, tableBodyId);

                                const modal = new bootstrap.Modal(modalElement);
                                modal.show();
                            }
                        });
                    });

                    function loadGroupMessages(groupId) {
                        fetch(`/chat-groups/${groupId}/messages`)
                            .then(response => response.json())
                            .then(messages => {
                                const chatBox = document.getElementById("chat-box");
                                chatBox.innerHTML = ""; // Clear existing messages

                                if (messages.length === 0) {
                                    chatBox.innerHTML = "<p class='text-muted'>No messages yet.</p>";
                                    return;
                                }

                                messages.forEach(msg => {
                                    addMessage(msg.user?.name, msg.message, msg.created_at);
                                });

                            })
                            .catch(err => {
                                console.error("Failed to fetch messages:", err);
                            });
                    }

                    function addMessage(sender, message, timestamp) {
                        const chatBox = document.getElementById("chat-box");

                        // Create the message container
                        const messageDiv = document.createElement("div");
                        messageDiv.classList.add("message", sender === "{{ auth()->user()->name }}" ? "sent" : "received");

                        // Create the inner message wrapper
                        const messageContainer = document.createElement("div");
                        messageContainer.classList.add("message-container");

                        // Create sender name element
                        const senderNameDiv = document.createElement("div");
                        senderNameDiv.textContent = sender;
                        senderNameDiv.style.fontSize = "12px";
                        senderNameDiv.style.fontWeight = "bold";
                        senderNameDiv.style.marginBottom = "4px";

                        // Create message bubble
                        const bubble = document.createElement("div");
                        bubble.classList.add("message-bubble");
                        bubble.textContent = message;

                        // Create timestamp element
                        const timestampDiv = document.createElement("div");
                        timestampDiv.classList.add("message-timestamp");

                        const dateObj = timestamp ? new Date(timestamp) : new Date();
                        const timeString = dateObj.toLocaleString([], {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true,
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });

                        timestampDiv.textContent = timeString;

                        // Append sender name, bubble & timestamp to message container
                        messageContainer.appendChild(senderNameDiv);
                        messageContainer.appendChild(bubble);
                        messageContainer.appendChild(timestampDiv);

                        // Append message container to messageDiv
                        messageDiv.appendChild(messageContainer);

                        // Append messageDiv to chatBox
                        chatBox.appendChild(messageDiv);

                        // Scroll to latest message
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }
                    let refreshInterval;

                    document.addEventListener("DOMContentLoaded", function () {
                        const groupCards = document.querySelectorAll(".group-card");
                        const leftIcon = '<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">'+
                            '<path d="M14 7.63636L14 4.5C14 4.22386 13.7761 4 13.5 4L4.5 4C4.22386 4 4 4.22386 4 4.5L4 19.5C4 19.7761 4.22386 20 4.5 20L13.5 20C13.7761 20 14 19.7761 14 19.5L14 16.3636" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'+
                            '<path d="M10 12L21 12M21 12L18.0004 8.5M21 12L18 15.5" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'+
                            '</svg>';

                        groupCards.forEach(card => {
                            card.addEventListener("click", function () {
                                const groupId = this.dataset.groupId;
                                const messageInput = document.getElementById("message-input");
                                    const sendButton = document.getElementById("send-button");
                                if (this.dataset.groupStatus==="0"){
                                    // To disable the text input field:
                                    messageInput.disabled = true
                                    // To disable the button:
                                    sendButton.disabled = true;
                                } else {
                                    messageInput.disabled = false
                                    // To disable the button:
                                    sendButton.disabled = false;
                                }

                                //loadGroupMessages(groupId);
                                let userLoggin = "{{ auth()->user()->id }}";

                                $('#group-id').val(groupId);
                                onloadGroupMessages(groupId,this.dataset.groupName,leftIcon,userLoggin,this.dataset.groupStatus);
                                if (refreshInterval) {
                                    clearInterval(refreshInterval);
                                }

                                refreshInterval = setInterval(() => {
                                    onloadGroupMessages(groupId,this.dataset.groupName,leftIcon,userLoggin);
                                }, 10000);

                            });
                        });
                    });
                    function onloadGroupMessages(groupId, groupName,leftIcon,userLoggin,groupStatus)
                    {
                        loadGroupMessages(groupId);
                        fetch(`/chat-groups/${groupId}/members`)
                            .then(response => response.json())
                            .then(members => {
                                const membersList = document.getElementById("member-list");
                                membersList.innerHTML = ""; // clear previous members

                                // ✅ use the passed groupName instead of this.dataset
                                document.getElementById("recipientLabel").textContent = groupName;

                                if (members.length === 0) {
                                    membersList.innerHTML = "<li>No members found.</li>";
                                    return;
                                }

                                let creator = "";
                                members.forEach(member => {
                                    if (member.is_creator === 1) {
                                        creator = member.user_id;
                                    }

                                    const li = document.createElement("li");
                                    li.classList.add("user");
                                    li.style.display = "flex";
                                    li.style.alignItems = "center";
                                    li.style.marginBottom = "8px";

                                    let userAvatar = usersAvatar.find(u => u.username.trim() === member.name);
                                    userAvatar = userAvatar ? userAvatar : '';

                                    const avatar = document.createElement("img");
                                    avatar.src = userAvatar.avatar || '{{ asset('assets/img/photos/default.png') }}';
                                    avatar.alt = `${member.name}'s avatar`;
                                    avatar.style.width = "40px";
                                    avatar.style.height = "40px";
                                    avatar.style.borderRadius = "50%";
                                    avatar.style.objectFit = "cover";
                                    avatar.style.marginRight = "10px";

                                    const nameSpan = document.createElement("span");
                                    nameSpan.textContent = member.name;
                                    if (member.is_creator === 1) {
                                        const badge = document.createElement("span");
                                        badge.textContent = "Admin";
                                        badge.classList.add("badge", "bg-primary", "ms-2");
                                        badge.style.fontSize = "0.7rem";
                                        badge.style.padding = "4px 6px";
                                        badge.style.borderRadius = "5px";

                                        nameSpan.appendChild(document.createTextNode(" ")); // space between name and badge
                                        nameSpan.appendChild(badge);
                                    }
                                    li.appendChild(avatar);
                                    li.appendChild(nameSpan);

                                    const removeBtn = document.createElement("button");
                                    removeBtn.classList.add("btn", "btn-sm");
                                    removeBtn.innerHTML = leftIcon;
                                    removeBtn.title = "Remove user";
                                    removeBtn.setAttribute("data-user-id", member.user_id);

                                    removeBtn.addEventListener("click", () => {
                                        if (!confirm(`Exit group?`)) return;

                                        fetch(`/chat-groups/${member.chat_group_id}/members/${member.user_id}`, {
                                            method: "DELETE",
                                            headers: {
                                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                                                "Accept": "application/json"
                                            }
                                        }).then(res => {
                                            if (res.ok) {
                                                li.remove();
                                                window.location.reload();
                                            } else {
                                                alert("Failed to remove user.");
                                            }
                                        });
                                    });

                                    // Conditionally append remove button
                                    if (creator === parseInt(userLoggin, 10)) {
                                        if (member.is_creator === 0) {
                                            // li.appendChild(removeBtn); // Uncomment if creator can remove anyone
                                        }
                                    } else {
                                        if (member.user_id === parseInt(userLoggin, 10)) {
                                            li.appendChild(removeBtn); // user can remove self
                                        }
                                    }

                                    membersList.appendChild(li);
                                });

                                // If creator, show invite button
                                if (creator === parseInt(userLoggin, 10)) {
                                    const li = document.createElement("li");
                                    if(groupStatus!=="0"){
                                        const inviteButton = document.createElement("button");
                                        inviteButton.classList.add("btn", "btn-secondary", "invite-user-btn");
                                        inviteButton.setAttribute("data-toggle", "modal");
                                        inviteButton.setAttribute("data-target", "#inviteUserModal" + groupId);
                                        inviteButton.setAttribute("data-group-id", groupId);
                                        inviteButton.textContent = "Add User";
                                        li.appendChild(inviteButton);
                                    }



                                    membersList.appendChild(li);
                                }
                            })
                            .catch(error => {
                                console.error("Failed to fetch members:", error);
                            });
                    }

                </script>


@endsection
