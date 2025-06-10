<?php
namespace App\Services;
use App\Models\Client;
use App\Models\User;
use App\Models\ClientActivityLog;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Notifications\UserNotification;
use App\Models\ClientRequest;

class ClientService 
{
    public function getClientDataForDatatable(Request $request) 
    {
         $clients = Client::distinct('clients.id')
            ->leftJoin('industry_categories', 'clients.industry_category_id', '=', 'industry_categories.id')
            ->leftJoin('countries', 'clients.country_id', '=', 'countries.id')
            ->leftJoin('users', 'clients.sales_person_id', '=', 'users.id')
            ->join('users as created', 'clients.created_id', '=', 'created.id')
            ->leftJoin('users as updated', 'clients.updated_id', '=', 'updated.id')
            ->leftJoin('client_requests', 'clients.id', '=', 'client_requests.client_id')
            ->with(['industryCategory', 'country', 'salesPerson', 'createdBy', 'updatedBy'])
            ->where('clients.data_status', 1)
            ->whereNot('clients.data_status', 0)
            ->select('clients.*');
            if ($request->filled('sales_person')) {
            if ($request->sales_person == '-') {
                $clients->whereNull('sales_person_id');
            } else {
                $clients->whereHas('salesPerson', function ($q) use ($request) {
                    $q->where('name', $request->sales_person);
                });
            }
        }

        if ($request->filled('industry')) {
            $clients->whereHas('industryCategory', function ($q) use ($request) {
                $q->where('name', $request->industry);
            });
        }

        if ($request->filled('country')) {
            $clients->whereHas('country', function ($q) use ($request) {
                $q->where('name', $request->country);
            });
        }

        return DataTables::of($clients)
            ->addIndexColumn()
            ->addColumn('industry', fn($client) => $client->industryCategory->name ?? '-')
            ->addColumn('country', fn($client) => $client->country->name ?? '-')
            ->addColumn('salesPerson', fn($client) => $client->salesPerson->name ?? '-')
            ->addColumn('sales_person_btn', function ($client) {
                $btnReasign = '';
                if (!is_null($client->salesPerson->name ?? null)) {
                    if (auth()->user()->can('edit-reasign-salesperson')) {
                        $btnReasign .= '<button class="btn btn-success btn-sm view-client-assign" data-id="' . $client->id . '"
                                                data-action="restore" >Reassign</button>';
                    }
                }

                return $btnReasign;
            })
            ->addColumn('image_path_img', function ($row) {
                if (!empty($row->image_path)) {
                    return asset('storage/' . $row->image_path);
                } else {
                    return null;
                    // return '<svg fill="#000000" width="80px" height="80px" viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:none;}</style></defs><title>no-image</title><path d="M30,3.4141,28.5859,2,2,28.5859,3.4141,30l2-2H26a2.0027,2.0027,0,0,0,2-2V5.4141ZM26,26H7.4141l7.7929-7.793,2.3788,2.3787a2,2,0,0,0,2.8284,0L22,19l4,3.9973Zm0-5.8318-2.5858-2.5859a2,2,0,0,0-2.8284,0L19,19.1682l-2.377-2.3771L26,7.4141Z"/><path d="M6,22V19l5-4.9966,1.3733,1.3733,1.4159-1.416-1.375-1.375a2,2,0,0,0-2.8284,0L6,16.1716V6H22V4H6A2.002,2.002,0,0,0,4,6V22Z"/><rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1" width="32" height="32"/></svg>';
                }
            })
            ->addColumn('image_path1', function ($row) {
                if (!empty($row->image_path)) {

                    return '<img src="' . asset('storage/' . $row->image_path) . '" alt="User Image" width="50" height="50" class="rounded-circle">';
                } else {
                    return '<svg fill="#000000" width="80px" height="80px" viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:none;}</style></defs><title>no-image</title><path d="M30,3.4141,28.5859,2,2,28.5859,3.4141,30l2-2H26a2.0027,2.0027,0,0,0,2-2V5.4141ZM26,26H7.4141l7.7929-7.793,2.3788,2.3787a2,2,0,0,0,2.8284,0L22,19l4,3.9973Zm0-5.8318-2.5858-2.5859a2,2,0,0,0-2.8284,0L19,19.1682l-2.377-2.3771L26,7.4141Z"/><path d="M6,22V19l5-4.9966,1.3733,1.3733,1.4159-1.416-1.375-1.375a2,2,0,0,0-2.8284,0L6,16.1716V6H22V4H6A2.002,2.002,0,0,0,4,6V22Z"/><rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1" width="32" height="32"/></svg>';
                }
            })
            ->addColumn('action', function ($row) {
                $btnEdit = '';
                $btn = '<div class="btn-group btn-group-vertical" role="group" aria-label="Basic mixed styles example"><button class="btn btn-info btn-sm view-client" data-id="' . $row->id . '">View</button>';

                $btnDelete1 = '';
                $btnDelete = '';
                $btnEdit = '';
                if ($row->sales_person_id == auth()->user()->id) {
                    $delete = $row->clientRequests
                        ->whereIn('data_status', [2, 4]) // Akan memfilter data_status 2 atau 4
                        ->sortByDesc('created_at')
                        ->first();
                    if (is_null($delete)) {
                        $btnDelete1 .= '<button class="btn btn-warning btn-sm view-client-delete" data-id="' . $row->id . '">Request to Delete</button></div>';
                    } else {
                        if (is_null($delete->approved_by)) {
                            $btnDelete1 .= '<button class="btn btn-danger btn-sm" disabled>Waiting Response</button>';
                        } else {
                            $btnDelete1 .= '<button class="btn btn-danger btn-sm confirm-action" data-id="' . $row->id . '"
                                                data-action="edit" >Open To Delete</button>';
                        }
                    }

                    $edit = $row->clientRequests
                        ->whereIn('data_status', [1, 3]) // Akan memfilter data_status 2 atau 4
                        ->sortByDesc('created_at')
                        ->first();
                    if (is_null($edit)) {
                        $btnEdit .= '<button class="btn btn-primary btn-sm view-client-edit" data-id="' . $row->id . '">Request to Edit</button>';
                    } else {
                        if (is_null($edit->approved_by)) {
                            $btnEdit .= '<button class="btn btn-primary btn-sm" disabled>Waiting Response</button>';
                        } else {
                            if ($edit->data_status == 3) {
                                $btnEdit .= '<a class="btn btn-success btn-sm" href="' . route('v1.client-database.edit', $row->id) . '">Open To Edit</a>';
                            } else {
                                $btnEdit .= '<a class="btn btn-success btn-sm" href="' . route('v1.client-database.edit', $row->id) . '">Open To Edit</a>';
                            }
                        }
                    }

                }
                if (auth()->user()->can('delete-client-database')) {
                    $btnDelete .= '<button class="btn btn-danger btn-sm confirm-action" data-id="' . $row->id . '"
                                                data-action="edit" >Delete</button>';
                }

                return $btn . $btnEdit . $btnDelete;
            })
            ->addColumn('quotation', fn($client) => 'Nil')
            ->addColumn('created_on', function ($client) {
                return $client->created_at->format('d M Y H:i') . '<br><small>' . $client->createdBy->name . '</small>';
            })
            ->addColumn('updated_on', function ($client) {
                if ($client->created_at != $client->updated_at) {
                    return $client->updated_at->format('d M Y h:i') . '<br><small></small>';
                } else {
                    return '';
                }
            })
            ->addColumn('remarks', function ($client) {
                $remarks = '<div class="remarks-scroll-container">';
                foreach ($client->remarks()->get() as $row) {
                    $remarks .= '<p>' . $row->content . '</p>';
                }
                return $remarks . '</div>';
            })
            ->addColumn('is_editable', function ($client) {
                if ($client->sales_person_id == auth()->user()->id) {
                    return true;
                } else {
                    return false;
                }
            })
            ->escapeColumns([])
            ->make(true);

    }

    public function assignSalesPerson(Request $request)
    {
        $client = Client::findOrFail($request->client_id);
        $oldSalespersonId = $client->sales_person_id;

        $validated = $request->validate([
            'sales_person_id' => 'required',
        ]);
        $validated['is_editable'] = $request->has('is_editable') ? 1 : 0;
        $validated['updated_by'] = auth()->user()->id;
        $validated['updated_at'] = date('Y-m-d H:i:s');
        // Save the client
        $client->update($validated);

        if ($validated['is_editable'] == 1) {
            $clientReq = new ClientRequest;
            $clientReq->data_status = 3;
            $clientReq->created_by = auth()->user()->id;
            $clientReq->client_id = $request->client_id;
            $clientReq->approved_by = auth()->user()->id;
            $clientReq->approved_at = date('Y-m-d H:i:s');
            $clientReq->remark = 'first time to client edit';
            $clientReq->save();
        }

        if ($oldSalespersonId != $validated['sales_person_id']) {
            $oldName = User::find($oldSalespersonId)?->name ?? 'N/A';
            $newName = User::find($validated['sales_person_id'])?->name ?? 'N/A';

            ClientActivityLog::create([
                'client_id' => $client->id,
                'action' => 'change',
                'activity' => "Salesperson changed from $oldName to $newName on " . now()->format('d M Y') . " at " . now()->format('g:i a') . " by " . auth()->user()->name
            ]);
            if ($oldName == 'N/A') {
                $user = \App\Models\User::findOrFail($validated['sales_person_id']);
                $user->notify(new UserNotification(
                    'There is a new client data being assigned to you.',
                    'accept',
                    route('v1.client-database.list')
                ));
            } else {
                $user = \App\Models\User::findOrFail($oldSalespersonId);
                $user->notify(new UserNotification(
                    'An existing client has been reassigned to ' . $newName . ' by ' . auth()->user()->name,
                    'accept',
                    route('v1.client-database.list')
                ));

                $user = \App\Models\User::findOrFail($validated['sales_person_id']);
                $user->notify(new UserNotification(
                    'An existing client has been reassigned to you by ' . auth()->user()->name,
                    'accept',
                    route('v1.client-database.list')
                ));
            }
        }

        if ($request->query('main')) {
                return response()->json(['success' => true, 'message' => 'Salesperson has reassigned successfully.']);
        } else {
                return response()->json(['success' => false, 'message' => 'Salesperson has assigned successfully.']);
        }
    }

    public function bulkAssignmentSalesperson(Request $request)
    {
        $validated = $request->validate([
            'client_ids' => 'required',
            'sales_person_id' => 'required',
        ]);

        $clientIdsArray = explode(',', $request->client_ids);
        $countFailed = 0;
        $countSuccess = 0;
        for ($i = 0; $i < count($clientIdsArray); $i++) {
            $client = Client::findOrFail($clientIdsArray[$i]);
            if (($request->status == 'reassign' && !is_null($client->sales_person_id)) || $request->status == 'assign') {
                $countSuccess++;
                $oldSalespersonId = $client->sales_person_id;
                $validated['is_editable'] = $request->has('is_editable') ? 1 : 0;
                $validated['updated_by'] = auth()->user()->id;
                $validated['updated_at'] = date('Y-m-d H:i:s');
                $validated['sales_person_id'] = $request->sales_person_id;
                $client->update($validated);

                if ($validated['is_editable'] == 1) {
                    $clientReq = new ClientRequest;
                    $clientReq->data_status = 3;
                    $clientReq->client_id = $clientIdsArray[$i];
                    $clientReq->created_by = auth()->user()->id;
                    $clientReq->approved_by = auth()->user()->id;
                    $clientReq->approved_at = date('Y-m-d H:i:s');
                    $clientReq->remark = 'first time to client edit';
                    $clientReq->save();
                }

                if ($oldSalespersonId != $request->sales_person_id) {
                    $oldName = User::find($oldSalespersonId)?->name ?? 'N/A';
                    $newName = User::find($request->sales_person_id)?->name ?? 'N/A';

                    ClientActivityLog::create([
                        'client_id' => $client->id,
                        'action' => 'change',
                        'activity' => "Salesperson changed from $oldName to $newName on " . now()->format('d M Y') . " at " . now()->format('g:i a') . " by " . auth()->user()->name
                    ]);
                    if ($oldName == 'N/A') {
                        $user = \App\Models\User::findOrFail($request->sales_person_id);
                        $user->notify(new UserNotification(
                            'There is a new client data being assigned to you.',
                            'accept',
                            route('v1.client-database.list')
                        ));
                    } else {
                        $user = \App\Models\User::findOrFail($oldSalespersonId);
                        $user->notify(new UserNotification(
                            'An existing client has been reassigned to ' . $newName . ' by ' . auth()->user()->name,
                            'accept',
                            route('v1.client-database.list')
                        ));

                        $user = \App\Models\User::findOrFail($request->sales_person_id);
                        $user->notify(new UserNotification(
                            'An existing client has been reassigned to you by ' . auth()->user()->name,
                            'accept',
                            route('v1.client-database.list')
                        ));
                    }
                }
            } else {
                $countFailed++;
            }

        }
        
        if ($request->query('main')) {
            return response()->json(['success' => true, 'message' => 'Salesperson has been assigned successfully.']);
        } else {
            if ($request->status == 'reassign') {
                if ($countSuccess > 0 && $countFailed === 0) { // Changed $countFailed < 0 to $countFailed === 0
                    return response()->json(['success' => true, 'message' => 'Salesperson has been reassigned successfully.']);                    
                } elseif ($countSuccess > 0 && $countFailed > 0) {
                    return response()->json(['success' => true, 'message' => 'Salesperson has been reassigned successfully.']);         
                } elseif ($countSuccess === 0 && $countFailed > 0) { // Changed $countSuccess == 0 to $countSuccess === 0 for strict comparison
                    return response()->json(['success' => false, 'message' => 'Salesperson has been reassigned but some have been rejected.']); // Removed '2' for cleaner message
                } else {
                    return response()->json(['success' => false, 'message' => 'No reassignment operations were performed or an unexpected state occurred.']);
                }
            } else {
                return response()->json(['success' => true, 'message' => 'Salesperson has been assigned successfully.']);
            }
        }
    }

    public function bulkDelete(Request $request)
    {
        // 1. Validate the incoming request
        $request->validate([
            'ids' => 'required|array',         // 'ids' must be a required array
            'ids.*' => 'required|integer|exists:clients,id', // Each ID must be an integer and exist in the 'clients' table
        ]);

        $clientIds = $request->input('ids');
        if($request->input('action')){
            foreach ($clientIds as $id) {

                $user = Client::find($id);
                $user->data_status = 1;
                $user->deleted_id = auth()->user()->id;
                $user->deleted_at = date('Y-m-d H:i:s');
                $user->save();

                $clientReq = ClientRequest::where(['client_id'=>$id,'data_status'=>4])->first();
                $clientReq->delete();
                ClientActivityLog::create([
                    'client_id' => $id,
                    'action' => 'restore',
                    'activity' => "Customer Data restored on " . now()->format('d M Y') . " at " . now()->format('g:i a') . " by " . auth()->user()->name
                ]);

            }
            return response()->json([
                    'success' => true,
                    'message' => "Successfully restore  client(s)."
                ]);

        } else {
            foreach ($clientIds as $id) {

                        $user = Client::find($id);
                        $user->data_status = 0;
                        $user->deleted_id = auth()->user()->id;
                        $user->deleted_at = date('Y-m-d H:i:s');
                        $user->save();

                        $clientReq = new ClientRequest;
                        $clientReq->client_id = $id;
                        $clientReq->data_status = 4;
                        $clientReq->created_by = auth()->user()->id;
                        $clientReq->remark = 'N/A';
                        $clientReq->save();

                    }

                    //if ($deletedCount > 0) {
                    return response()->json([
                        'success' => true,
                        'message' => "Successfully deleted  client(s)."
                    ]);
        }

    }

    public function bulkRequestToEdit(Request $request)
    {
        $validated = $request->validate([
            'edit_client_ids' => 'required',
        ]);

        $clientIdsArray = explode(',', $request->edit_client_ids);
        $countFailed = 0;
        $countSuccess = 0;
        for ($i = 0; $i < count($clientIdsArray); $i++) {
            $client = Client::findOrFail($clientIdsArray[$i]);
            if ($client->sales_person_id === auth()->user()->id) {
                $countSuccess++;

                $client_req = new ClientRequest;
                $client_req->client_id = $client->id;
                $client_req->remark = $request->input('edit_reason');
                $client_req->data_status = $request->input('edit_status') == 'delete' ? 2 : 1;
                $client_req->created_by = auth()->user()->id;
                $client_req->save();
                $msg = $request->input('status') == 'delete' ? 'Client delete requested successfully!' : 'Client edit requested successfully!';
                if ($request->input('status') == 'delete') {
                    $msg = 'Client delete requested successfully!';
                } else {
                    $msg = 'Client update requested successfully!';
                    $users = \App\Models\User::permission('view-edit-request')->get();
                    if ($users) {
                        foreach ($users as $user) {
                            $user->notify(new UserNotification(
                                auth()->user()->name . ' requested to edit an existing client data',
                                'accept',
                                route('v1.client-database.request-list')
                            ));
                        }
                    }
                }

            } else {
                $countFailed++;
            }

        }

        // DB::commit();
        if ($countSuccess > 0 && $countFailed === 0) { // Changed $countFailed < 0 to $countFailed === 0
            return response()->json([
                        'success' => true,
                        'success_message' => "Request to edit has been save successfully.",
                        'reject_message'=> null
                    ]);
        } elseif ($countSuccess > 0 && $countFailed > 0) {
            return response()->json([
                        'success' => true,
                        'success_message' => "Request to edit has been save successfully.",
                        'reject_message'=>'Request to edit has been processed but some have been rejected because you not assigned as sales person.'
                    ]); // Removed '1' for cleaner message
        } elseif ($countSuccess === 0 && $countFailed > 0) { // Changed $countSuccess == 0 to $countSuccess === 0 for strict comparison
           return response()->json([
                        'success' => true,
                        'success_message' => null,
                        'reject_message'=>'Request to edit has been processed but some have been rejected because you not assigned as sales person.'
                    ]); // Removed '
        } else {
             return response()->json([
                        'success' => true,
                        'success_message' => null,
                        'reject_message'=>null
                    ]); // Removed '
        }
    }

}