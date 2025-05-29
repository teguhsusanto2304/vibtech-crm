<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientRequest;
use App\Models\Country;
use App\Models\IndustryCategory;
use App\Models\User;
use App\Models\ClientActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use App\Notifications\UserNotification;
use Auth;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-client-database', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-client-database', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-client-database', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-client-database', ['only' => ['destroy']]);
    }

    public function index()
    {
        $salesPersonNotifications = null;
        $clientDataNotifications = null;
        $recycleBinNotification = null;
        $requestNotifications = null;
        if (\App\Models\User::permission('view-salesperson-assignment')){
            $salesPersonNotifications = DB::table('notifications')->where('type', 'App\Notifications\UserNotification')
                ->where('notifiable_type', 'App\Models\User')
                ->whereRaw("JSON_UNQUOTE(data->'$.url') LIKE ?", ['%v1/client-database/assignment-salesperson/list%'])
                ->whereNull('read_at')
                ->where('notifiable_id',auth()->user()->id)
                ->count();
        }

        if (\App\Models\User::permission('view-client-recycle')){
            $recycleBinNotification = DB::table('notifications')->where('type', 'App\Notifications\UserNotification')
                ->where('notifiable_type', 'App\Models\User')
                ->whereRaw("JSON_UNQUOTE(data->'$.url') LIKE ?", ['%v1/client-database/recycle-bin/list%'])
                ->whereNull('read_at')
                ->where('notifiable_id',auth()->user()->id)
                ->count();
        }

        $clientDataNotifications = DB::table('notifications')->where('type', 'App\Notifications\UserNotification')
                            ->where('notifiable_type', 'App\Models\User')
                            ->whereRaw("JSON_UNQUOTE(data->'$.url') LIKE ?", ['%v1/client-database/list%'])
                            ->whereNull('read_at')
                            ->where('notifiable_id',auth()->user()->id)
                            ->count();

        $requestNotifications = DB::table('notifications')->where('type', 'App\Notifications\UserNotification')
                            ->where('notifiable_type', 'App\Models\User')
                            ->whereRaw("JSON_UNQUOTE(data->'$.url') LIKE ?", ['%v1/client-database/request-list%'])
                            ->whereNull('read_at')
                            ->where('notifiable_id',auth()->user()->id)
                            ->count();



        return view('client_database.index',compact('requestNotifications','recycleBinNotification','clientDataNotifications','salesPersonNotifications'))->with('title', 'Client Database')->with('breadcrumb', ['Home', 'Client Database']);
    }

    public function create()
    {
        return view('client_database.form', [
            'industries' => IndustryCategory::all(),
            'countries' => Country::all(),
            'salesPeople' => User::all(),
        ])->with('title', 'Create Client Database')->with('breadcrumb', ['Home', 'Client Database', 'Create a Client Data']);
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);

        return view('client_database.edit', [
            'client' => $client,
            'industries' => IndustryCategory::all(),
            'countries' => Country::all(),
            'salesPeople' => User::all(),
            'title' => 'Edit Client Database',
            'breadcrumb' => ['Home', 'Client Database', 'Edit a Client Data'],
        ]);
    }

    public function preview($id)
    {
        $clientRequest = ClientRequest::findOrFail($id);
        $client = Client::findOrFail($clientRequest->client_id);

        return view('client_database.show', [
            'client' => $client,
            'clientRequest' => $clientRequest,
            'title' => 'Preview Edit Request Client Database',
            'breadcrumb' => ['Home', 'Client Database', 'Preview a Client Data'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('clients')->where(function ($query) use ($request) {
                    return $query->where(['company' => $request->company, 'data_status' => 1]);
                }),
            ],
            'company' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:clients',
            'office_number' => 'required|numeric',
            'mobile_number' => 'nullable|numeric',
            'job_title' => 'nullable|string|max:255',
            'industry_category_id' => 'required|exists:industry_categories,id',
            'country_id' => 'required|exists:countries,id',
            'contact_for_id' => 'required|exists:users,id',
            'image_path' => 'nullable|image|max:2048', // Accept only image files
        ]);

        // Handle file upload if exists
        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('clients', 'public');
            $validated['image_path'] = $path;
        }
        $validated['created_id'] = auth()->user()->id;

        // Save the client
        Client::create($validated);
            $users = \App\Models\User::permission('view-salesperson-assignment') // Spatie magic filter
                    ->get(); // example
            foreach ($users as $user) {
                    $user->notify(new UserNotification(
                                'There are 1 new client data inserted, please assign salesperson/s to the data/s',
                                'accept',
                                route('v1.client-database.assignment-salesperson.list')
                            ));
                }

        return redirect()->route('v1.client-database.list')->with('success', 'Client created successfully.');
    }

    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'office_number' => 'required|numeric',
            'mobile_number' => 'nullable|numeric',
            'job_title' => 'nullable|string|max:255',
            'industry_category_id' => 'required|exists:industry_categories,id',
            'country_id' => 'required|exists:countries,id',
            // 'sales_person_id' => 'required|exists:users,id',
            'image_path' => 'nullable|image|max:2048', // Accept only image files
        ]);

        // Handle file upload if exists
        if ($request->hasFile('image_path')) {
            // Delete old image if exists
            if ($client->image_path && Storage::disk('public')->exists($client->image_path)) {
                Storage::disk('public')->delete($client->image_path);
            }

            // Store new image
            $imagePath = $request->file('image_path')->store('clients', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Save the client
        //$client->update($validated);

        //$clientReq = ClientRequest::where(['client_id' => $id, 'data_status' => 3])->first();
        //$clientReq->data_status = 1;
        //$clientReq->save();

        return redirect()->route('v1.client-database.list')->with('success', 'Client updated successfully.');
    }

    public function import(Request $request)
    {

        $i = 0;
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'contact_for_id' => 'required|exists:users,id',
        ]);

        $file = $request->file('csv_file');
        $duplicates = [];

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data = array_combine($header, $row);

                if (!empty($data['name']) && !empty($data['email'])) {
                    // Check if email already exists
                    $exists = Client::where('email', $data['email'])->exists();

                    if ($exists) {
                        $duplicates[] = $data['email']; // Track duplicates
                        continue; // Skip insertion
                    }

                    // Store data
                    $i++;
                    Client::create([
                        'name' => $data['name'] ?? null,
                        'position' => $data['position'] ?? null,
                        'email' => $data['email'] ?? null,
                        'office_number' => $data['office_number'] ?? null,
                        'mobile_number' => $data['mobile_number'] ?? null,
                        'job_title' => $data['job_title'] ?? null,
                        'company' => $data['company'] ?? null,
                        'contact_for_id' => $request->input('contact_for_id'),
                        'created_id' => auth()->user()->id,
                    ]);
                }
            }

            fclose($handle);
        }

        // Redirect with duplicate email info
        if (!empty($duplicates)) {
            return redirect()
                ->route('v1.client-database.create')
                ->with('error_import', 'Some emails were duplicated and skipped: ' . implode(', ', $duplicates));
        }

        $users = \App\Models\User::permission('view-salesperson-assignment')
                    ->get(); // example
            foreach ($users as $user) {
                    $user->notify(new UserNotification(
                                'There are '.$i.' new client data inserted, please assign salesperson/s to the data/s',
                                'accept',
                                route('v1.client-database.assignment-salesperson.list')
                            ));
                }

        return redirect()
            ->route('v1.client-database.list')
            ->with('success', 'CSV imported successfully!');
    }


    public function updateRequest(Request $request, $id)
    {
        DB::beginTransaction();
        $clientReq = ClientRequest::where(['client_id' => $id, 'data_status' => 3])->first();
        $clientReq->delete();

        $client = Client::findOrFail($clientReq->client_id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'office_number' => 'required|numeric',
            'mobile_number' => 'nullable|numeric',
            'job_title' => 'nullable|string|max:255',
            'industry_category_id' => 'nullable',
            'country_id' => 'nullable',
            'sales_person_id' => 'nullable',
            'image_path' => 'nullable', // Accept only image files
        ]);

        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('clients', 'public');
            $validated['image_path'] = $path;
        }
        // Save the client
        $client->update($validated);

        ClientActivityLog::create([
            'client_id' => $client->id,
            'action' => 'update',
            'activity' => "Client Information updated on " . now()->format('d M Y') . " at " . now()->format('g:i a') . " by " . auth()->user()->name
        ]);

        DB::commit();

        return redirect()->route('v1.client-database.list')->with('success', 'Client updated successfully.');
    }

    public function deleteRequest($id)
    {
        $client = Client::findOrFail($id);

        return view('client_database.delete', [
            'client' => $client,
            'industries' => IndustryCategory::all(),
            'countries' => Country::all(),
            'salesPeople' => User::all(),
            'title' => 'Edit Client Database',
            'breadcrumb' => ['Home', 'Client Database', 'Edit a Client Data'],
        ]);
    }

    public function list()
    {
        $notificationsToMarkAsRead = DB::table('notifications')
            ->where('type', 'App\Notifications\UserNotification')
            ->where('notifiable_type', 'App\Models\User')
            ->whereRaw("JSON_UNQUOTE(data->'$.url') LIKE ?", ['%v1/client-database/list%'])
            ->whereNull('read_at')
            ->where('notifiable_id', Auth::id())
            ->get();

        if ($notificationsToMarkAsRead->isNotEmpty()) {
            $notificationIds = $notificationsToMarkAsRead->pluck('id')->toArray();

            DB::table('notifications')
                ->whereIn('id', $notificationIds)
                ->update(['read_at' => now()]);
        }

        return view('client_database.list', [
            'industries' => IndustryCategory::all(),
            'countries' => Country::all(),
            'salesPersons' => User::all(),
        ])->with('title', 'List of Client Database')->with('breadcrumb', ['Home', 'Client Database', 'List of Client Database']);

    }

    public function assignmentList()
    {
        if (\App\Models\User::permission('view-salesperson-assignment')){
            $notificationsToMarkAsRead = DB::table('notifications')
                ->where('type', 'App\Notifications\UserNotification')
                ->where('notifiable_type', 'App\Models\User')
                ->whereRaw("JSON_UNQUOTE(data->'$.url') LIKE ?", ['%v1/client-database/salesperson-assignment/list%'])
                ->whereNull('read_at')
                ->where('notifiable_id', Auth::id())
                ->get();

            if ($notificationsToMarkAsRead->isNotEmpty()) {
                $notificationIds = $notificationsToMarkAsRead->pluck('id')->toArray();

                DB::table('notifications')
                    ->whereIn('id', $notificationIds)
                    ->update(['read_at' => now()]);
            }
        }
        return view('client_database.assignment_list', [
            'industries' => IndustryCategory::all(),
            'countries' => Country::all(),
            'salesPersons' => User::all(),
        ])->with('title', 'List of Salesperson Assignment')->with('breadcrumb', ['Home', 'Client Database', 'List of Salesperson Assignment']);

    }

    public function recycleBinlist()
    {
        return view('client_database.recycle_bin.list', [
            'industries' => IndustryCategory::all(),
            'countries' => Country::all(),
            'salesPersons' => User::all(),
        ])->with('title', 'List of Client Database Recycle Bin')->with('breadcrumb', ['Home', 'Client Database', 'List of Client Database Recycle Bin']);

    }

    public function updateRequestList()
    {
        return view('client_database.request.list', [
            'industries' => IndustryCategory::all(),
            'countries' => Country::all(),
            'salesPersons' => User::all(),
        ])->with('title', 'List of Edit Request')->with('breadcrumb', ['Home', 'Client Database', 'List of Edit Request']);

    }

    public function toggleStatus(Request $request)
    {
        $user = Client::find($request->id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        $user->data_status = 0;
        $user->deleted_id = auth()->user()->id;
        $user->deleted_at = date('Y-m-d H:i:s');
        $user->save();

        $clientReq = new ClientRequest;
        $clientReq->client_id = $request->id;
        $clientReq->data_status = 4;
        $clientReq->created_by = auth()->user()->id;
        $clientReq->remark = 'N/A';
        $clientReq->save();

        ClientActivityLog::create([
            'client_id' => $request->id,
            'action' => 'delete',
            'activity' => "Client Information delete on " . now()->format('d M Y') . " at " . now()->format('g:i a') . " by " . auth()->user()->name
        ]);

        $allUsers = User::all();
        $users = \App\Models\User::permission('view-client-recycle')->get();
        if($users){
            foreach($users as $user){
                $user->notify(new UserNotification(
                                            'An existing client data has been deleted by '.auth()->user()->name.', the data will be stored in the recycle bin for 60 days.',
                                            'accept',
                                            route('v1.client-database.recycle-bin.list')
                ));
            }
        }
        $usersWithoutPermission = $allUsers->diff($users);
            foreach ($usersWithoutPermission as $user) {
                $user->notify(new UserNotification(
                    'An existing client data has been deleted by '.auth()->user()->name,
                    'accept',
                    route('v1.client-database.list')
                ));
            }

        return response()->json(['success' => true, 'message' => 'Client deleted successfully']);
    }

    public function assignmentSalesperson(Request $request)
    {
        // DB::beginTransaction();

        // try {
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
            if($oldName=='N/A'){
                $user = \App\Models\User::findOrFail($validated['sales_person_id']);
                $user->notify(new UserNotification(
                                'There is a new client data being assigned to you.',
                                'accept',
                                route('v1.client-database.list')
                            ));
            } else {
                $user = \App\Models\User::findOrFail($oldSalespersonId);
                $user->notify(new UserNotification(
                                'An existing client has been reassigned to '.$newName.' by '.auth()->user()->name,
                                'accept',
                                route('v1.client-database.list')
                            ));

                $user = \App\Models\User::findOrFail($validated['sales_person_id']);
                $user->notify(new UserNotification(
                                'An existing client has been reassigned to you by '.auth()->user()->name,
                                'accept',
                                route('v1.client-database.list')
                            ));
            }

        }

        // DB::commit();
        if ($request->query('main')) {
            return redirect()->route('v1.client-database.list')->with('success', 'Salesperson has assigned successfully.');
        } else {
            return redirect()->route('v1.client-database.assignment-salesperson.list')->with('success', 'Salesperson has assigned successfully.');
        }

        // } catch (\Illuminate\Validation\ValidationException $e) {
        // Rollback transaction on validation error
        //    DB::rollBack();
        //    return redirect()->back()->withErrors($e->errors())->withInput();
        // } catch (\Exception $e) {
        // Rollback transaction on any other exception
        //    DB::rollBack();
        // Log the error for debugging
        // \Log::error('Error updating client: ' . $e->getMessage(), ['exception' => $e]);
        //    return redirect()->back()->with('error', 'Failed to update client. Please try again.')->withInput();
        // }
    }

    public function deleteFromRequest(Request $request)
    {
        $clientRequest = ClientRequest::find($request->id);
        if(!$clientRequest){
            $clientRequest = ClientRequest::where('client_id',$request->id)->first();
        }

        if ($request->action == 'edit-reject') {
            $clientRequest->data_status == 0;
            $msg = 'Request to edit client data has rejected successfully';
                        $user = \App\Models\User::find($clientRequest->created_by);
            $user->notify(new UserNotification(
                                'Your request to edit an existing client data has been rejected.',
                                'accept',
                                route('v1.client-database.list')
                            ));
        } elseif ($request->action == 'edit') {
            $clientRequest->data_status = 3;
            $msg = 'Request to edit client data has approved successfully';
            $user = \App\Models\User::find($clientRequest->created_by);
            $user->notify(new UserNotification(
                                'Your request to edit an existing client data has been approved.',
                                'accept',
                                route('v1.client-database.list')
                            ));
        } elseif ($request->action == 'restore') {
            $clientRequest->data_status = 1;
            $msg = 'client data has restored successfully';
            ClientActivityLog::create([
                'client_id' => $clientRequest->client_id,
                'action' => 'restore',
                'activity' => "Customer Data restored on " . now()->format('d M Y') . " at " . now()->format('g:i a') . " by " . auth()->user()->name
            ]);
        } elseif ($request->action == 'permanent_delete') {
            $clientRequest->data_status = 0;
            $msg = 'client data has permanent deleted successfully';
        }
        $clientRequest->approved_by = auth()->user()->id;
        $clientRequest->approved_at = date('Y-m-d H:i:s');
        $clientRequest->save();

        if ($clientRequest->data_status == 1 || $clientRequest->data_status == 0) {
            $client = Client::findOrFail($clientRequest->client_id);
            $client->data_status = 1;
            $client->save();

            $clientRequestDel = ClientRequest::find($clientRequest->id);
            $clientRequestDel->delete();
        }

        return response()->json(['success' => true, 'message' => $msg]);
        if (!$clientRequest) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }
    }

    public function getClientsData(Request $request)
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
            ->escapeColumns([])
            ->make(true);
    }

    public function getClientRequestsData(Request $request)
    {
        $requestType = $request->input('request_type');
        $clientReqs = ClientRequest::with([
            'client', // Eager load the Client model
            'client.industryCategory', // Nested eager load
            'client.country',        // Nested eager load
            'client.salesPerson',    // Nested eager load (Client's sales person)
            'client.createdBy',      // Nested eager load (Client's creator)
            'client.updatedBy',      // Nested eager load (Client's updater)
            'createdBy',             // Eager load the User who created the ClientRequest (this is the 'created_by' from ClientRequest)
            'approvedBy',            // Eager load the User who approved the ClientRequest
        ]);
        if ($requestType == 1) { // Edit Requested
            $clientReqs->where('data_status', 1);
            $clientReqs->WhereNull('approved_by');
        } elseif ($requestType == 2) { // Delete Requested
            $clientReqs->whereIn('data_status', [2, 4]);
        } else {
            // Default atau error handling jika request_type tidak valid
            // Misalnya, kembalikan koleksi kosong atau throw error
            $clientReqs->where('data_status', -1); // Pastikan tidak ada hasil
        }

        // Filters based on Client's relationships (correctly using whereHas with dot notation)
        if ($request->filled('sales_person')) {
            if ($request->sales_person == '-') {
                $clientReqs->whereNull('sales_person_id');
            } else {
                $clientReqs->whereHas('salesPerson', function ($q) use ($request) {
                    $q->where('name', $request->sales_person);
                });
            }
        }

        if ($request->filled('industry')) {
            $clientReqs->whereHas('client.industryCategory', function ($q) use ($request) {
                $q->where('name', $request->industry);
            });
        }

        if ($request->filled('country')) {
            $clientReqs->whereHas('client.country', function ($q) use ($request) {
                $q->where('name', $request->country);
            });
        }

        $data = $clientReqs->get();

        return DataTables::of($data)
            ->addIndexColumn()
            // Access client data through the 'client' relationship
            ->addColumn('client_name', fn($row) => $row->client->name ?? '-')
            ->addColumn('client_company', fn($row) => $row->client->company ?? '-')
            ->addColumn('client_email', fn($row) => $row->client->email ?? '-')
            ->addColumn('industry', fn($row) => $row->client->industryCategory->name ?? '-')
            ->addColumn('country', fn($row) => $row->client->country->name ?? '-')
            ->addColumn('sales_person', fn($row) => $row->client->salesPerson->name ?? '-') // Access via client relationship
            ->addColumn('image_path_img', function ($row) {
                // This refers to the image_path on the Client model
                if (!empty($row->client->image_path)) {
                    return asset('storage/' . $row->client->image_path);
                } else {
                    return null;
                }
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group" role="group" aria-label="Basic mixed styles example">';
                // Ensure $row->client exists before accessing its properties
                $clientId = $row->id ?? null;

                if ($clientId) { // Only show buttons if a related client exists
                    $btn .= '<button class="btn btn-info btn-sm view-client" data-id="' . $row->client->id . '">View Client</button>';

                    // Logic for ClientRequest specific actions (edit/delete request)
                    // Assuming $row is a ClientRequest model instance
                    if ($row->data_status == 1) { // This is the data_status of the ClientRequest
                        if (is_null($row->approved_by)) {
                            $btn .= '<button class="btn btn-primary btn-sm confirm-action" data-id="' . $clientId . '"
                                                data-action="edit"  data-text="approve">Approve</button>';
                            $btn .= '<button class="btn btn-danger btn-sm confirm-action" data-id="' . $clientId . '"
                                                data-action="edit-reject" data-text="reject">Reject</button>';
                        } else {
                            // This button would typically lead to approving/reviewing the edit request
                            if (is_null($row->updated_by)) {
                                $btn .= '<a class="btn btn-success btn-sm" disabled>Has Approved</a>';
                            } else {
                                $btn .= '<a class="btn btn-success btn-sm" disabled>Has Updated</a>';
                            }
                        }
                    } elseif ($row->data_status == 3) { // This is the data_status of the ClientRequest
                        if (!is_null($row->approved_by)) {
                            $btn .= '<button class="btn btn-success btn-sm" disabled>Has Approved</button>';
                        }
                    } elseif ($row->data_status == 2) { // This is the data_status of the ClientRequest
                        if (is_null($row->approved_by)) {
                            $btn .= '<button class="btn btn-danger btn-sm confirm-action" data-id="' . $clientId . '"
                                                data-action="delete">Approve</button>';
                        }
                    } elseif ($row->data_status == 4) { // This is the data_status of the ClientRequest
                        if (!is_null($row->approved_by)) {
                            $btn .= '<button class="btn btn-danger btn-sm" disabled>Has Approved</button>';
                        }
                    }
                } else {
                    $btn .= '<span class="text-muted">No Client Linked</span>';
                }
                $btn .= '</div>'; // Close the btn-group

                return $btn;
            })
            ->addColumn('name', fn($row) => $row->client->name) // This would be from Client, not ClientRequest
            ->addColumn('company', fn($row) => $row->client->company)
            ->addColumn('email', fn($row) => $row->client->email)
            ->addColumn('office_number', fn($row) => $row->client->office_number)
            ->addColumn('mobile_number', fn($row) => $row->client->mobile_number)
            ->addColumn('job_title', fn($row) => $row->client->job_title)
            ->addColumn('created_on', fn($row) => $row->created_at->format('d M Y')) // created_at of ClientRequest
            ->addColumn('created_by', function ($row) {
                // Access the createdBy relationship on the ClientRequest model
                return $row->created_at->format('d M Y') . ' <p><span class="badge bg-info"><small>' . ($row->createdBy->name ?? 'N/A') . '</small></span></p>';
            })
            // Added the remark column, accessing it directly from the ClientRequest model
            ->addColumn('remark', fn($row) => $row->remark ?? '-')
            ->addColumn('request_status', function ($row) {
                // This refers to the data_status of the ClientRequest
                if ($row->data_status == 1) {
                    return '<span class="badge bg-primary">Edit Request</span>';
                } elseif ($row->data_status == 2) {
                    return '<span class="badge bg-danger">Delete Request</span>';
                } elseif ($row->data_status == 3) {
                    return '<span class="badge bg-success">Approved/Updated</span>'; // Assuming 3 means approved
                } elseif ($row->data_status == 4) {
                    return '<span class="badge bg-warning">Deleted</span>'; // Assuming 4 means deleted
                }

                return '<span class="badge bg-secondary">Other Status</span>';
            })
            // IMPORTANT: Specify which columns contain raw HTML to prevent escaping
            ->escapeColumns([])
            ->make(true);
    }

    public function getAssignmentSalespersonData(Request $request)
    {
        $clients = Client::leftJoin('industry_categories', 'clients.industry_category_id', '=', 'industry_categories.id')
            ->leftJoin('countries', 'clients.country_id', '=', 'countries.id')
            ->leftJoin('users', 'clients.sales_person_id', '=', 'users.id')
            ->leftJoin('users as contact_for', 'clients.contact_for_id', '=', 'contact_for.id')
            ->join('users as created', 'clients.created_id', '=', 'created.id')
            ->with(['industryCategory', 'country', 'salesPerson', 'createdBy', 'contactFor'])
            ->where('clients.data_status', 1)
            ->whereNull('clients.sales_person_id')
            ->select('clients.*')
            ->orderBy('created_at','ASC');

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
            ->addColumn('sales_person', fn($client) => $client->salesPerson->name ?? '-')
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
                $btn = '<div class="btn-group btn-group-vertical" role="group" aria-label="Basic mixed styles example"><button class="btn btn-info btn-sm view-client" data-id="' . $row->id . '">View</button>';

                if ($row->sales_person_id == auth()->user()->id) {
                    $btnDel = ' <a href="javascript:void(0)" class="confirm-action btn btn-danger btn-sm"
                                data-id="' . $row->id . '"
                                data-action="deactivate">Delete</a></div>';
                    $btn .= '<a href="' . route('v1.client-database.edit', ['id' => $row->id]) . '" class="btn btn-primary btn-sm">Request to Edit</a>';
                    $btn .= '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><button class="btn btn-danger btn-sm view-client-delete" data-id="' . $row->id . '">Request to Delete</button>';
                } else {
                    $btn1 = ' <a href="javascript:void(0)" class="confirm-action btn btn-success btn-sm"
                                data-id="' . $row->id . '"
                                data-action="activate">Activate</a>';
                }

                return $btn;
            })
            ->addColumn('contact_for', fn($client) => $client->contactFor->name ?? '-')
            ->addColumn('assign_to', function ($client) {
                if (empty($client->sales_person_id)) {
                    $return = ' <a href="javascript:void(0)" class="view-client-assign btn btn-success btn-sm"
                                data-id="' . $client->id . '"
                                data-action="Assign">Start Assignment</a>';
                } else {
                    $return = $client->salesPerson->name;
                }

                return $return;
            })
            ->addColumn('created_on', function ($client) {
                return $client->createdBy->name . '<br><small>' . $client->created_at->format('d M Y H:n') . '</small>';
            })
            ->addColumn('updated_on', function ($client) {
                if ($client->created_at != $client->updated_at) {
                    return $client->updated_at->format('d M Y');
                } else {
                    return '';
                }
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getClientHasRemovedData(Request $request)
    {
        $clients = Client::distinct('clients.id')
            ->leftJoin('industry_categories', 'clients.industry_category_id', '=', 'industry_categories.id')
            ->leftJoin('countries', 'clients.country_id', '=', 'countries.id')
            ->leftJoin('users', 'clients.sales_person_id', '=', 'users.id')
            ->join('users as created', 'clients.created_id', '=', 'created.id')
            ->leftJoin('users as updated', 'clients.updated_id', '=', 'updated.id')
            ->leftJoin('users as deleted', 'clients.updated_id', '=', 'deleted.id')
            ->leftJoin('client_requests', 'clients.id', '=', 'client_requests.client_id')
            ->with(['industryCategory', 'country', 'salesPerson', 'createdBy', 'updatedBy','deletedBy'])
            ->where('client_requests.data_status', 4)
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
            ->addColumn('sales_person', fn($client) => $client->salesPerson->name ?? '-')
            ->addColumn('image_path_img', function ($row) {
                if (!empty($row->image_path)) {
                    return asset('storage/' . $row->image_path);
                } else {
                    return null;
                    // return '<svg fill="#000000" width="80px" height="80px" viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:none;}</style></defs><title>no-image</title><path d="M30,3.4141,28.5859,2,2,28.5859,3.4141,30l2-2H26a2.0027,2.0027,0,0,0,2-2V5.4141ZM26,26H7.4141l7.7929-7.793,2.3788,2.3787a2,2,0,0,0,2.8284,0L22,19l4,3.9973Zm0-5.8318-2.5858-2.5859a2,2,0,0,0-2.8284,0L19,19.1682l-2.377-2.3771L26,7.4141Z"/><path d="M6,22V19l5-4.9966,1.3733,1.3733,1.4159-1.416-1.375-1.375a2,2,0,0,0-2.8284,0L6,16.1716V6H22V4H6A2.002,2.002,0,0,0,4,6V22Z"/><rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1" width="32" height="32"/></svg>';
                }
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group btn-group-vertical" role="group" aria-label="Basic mixed styles example"><button class="btn btn-info btn-sm view-client" data-id="' . $row->id . '">View</button>';

                $btnDelete = '';
                if (auth()->user()->can('delete-client-database')) {
                    $btnDelete .= '<button class="btn btn-danger btn-sm confirm-action" data-id="' . $row->id . '"
                                                data-action="permanent delete" >Delete</button>';
                }

                $btnRestore = '';
                if (auth()->user()->can('restore-client-database')) {
                    $btnRestore .= '<button class="btn btn-success btn-sm confirm-action" data-id="' . $row->id . '"
                                                data-action="restore" >Restore</button>';
                }

                return $btn .  $btnRestore;
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
            ->addColumn('deleted_on', function ($client) {
                return $client->deleted_at->format('d M Y H:i') . '<br><small>' . $client->deletedBy->name . '</small>';
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getClientDetail(Request $request, $id)
    {
        $client = Client::with(['industryCategory', 'country', 'salesPerson'])->findOrFail($id);
        $delete = $request->query('delete');
        $assign = $request->query('assign');
        $users = null;
        if (!empty($assign)) {
            $users = User::all();

            return view('client_database.partials.detail', compact('users'));
        } else {
            return view('client_database.partials.detail', compact('client', 'delete', 'users'));
        }
    }

    public function clientDataRequest(Request $request)
    {
        $client_req = new ClientRequest;
        $client_req->client_id = $request->input('client_id');
        $client_req->remark = $request->input('remark');
        $client_req->data_status = $request->input('status') == 'delete' ? 2 : 1;
        $client_req->created_by = auth()->user()->id;
        $client_req->save();
        $msg = $request->input('status') == 'delete' ? 'Client delete requested successfully!' : 'Client edit requested successfully!';
        if ($request->input('status') == 'delete') {
            $msg = 'Client delete requested successfully!';
        } else {
            $msg = 'Client update requested successfully!';
            $users = \App\Models\User::permission('view-edit-request')->get();
            if ($users){
                foreach ($users as $user) {
                    $user->notify(new UserNotification(
                                auth()->user()->name.' requested to edit an existing client data',
                                'accept',
                                route('v1.client-database.request-list')
                            ));
                }
            }
        }

        return redirect()->route('v1.client-database.list')->with('success', $msg);
    }

    public function exportCsv(Request $request)
    {
        $clients = Client::with(['industryCategory', 'country', 'salesPerson'])
            ->when($request->sales_person, fn($q) => $q->whereHas('salesPerson', fn($q) => $q->where('name', $request->sales_person)))
            ->when($request->industry, fn($q) => $q->whereHas('industryCategory', fn($q) => $q->where('name', $request->industry)))
            ->when($request->country, fn($q) => $q->whereHas('country', fn($q) => $q->where('name', $request->country)))
            ->get();

        $csvHeader = [
            'Name',
            'Company',
            'Email',
            'Office Number',
            'Mobile Number',
            'Job Title',
            'Industry',
            'Country',
            'Sales Person',
            'Quotation',
            'Created On',
            'Updated On',
        ];

        $filename = 'clients_export.csv';
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $csvHeader);

        foreach ($clients as $client) {
            fputcsv($handle, [
                $client->name,
                $client->company,
                $client->email,
                $client->office_number,
                $client->mobile_number,
                $client->job_title,
                $client->industryCategory->name ?? '-',
                $client->country->name ?? '-',
                $client->salesPerson->name ?? '-',
                $client->quotation,
                $client->created_on,
                $client->updated_on,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    public function exportPdf(Request $request)
    {
        $clients = Client::with(['industryCategory', 'country', 'salesPerson'])
            ->when($request->sales_person, fn($q) => $q->whereHas('salesPerson', fn($q) => $q->where('name', $request->sales_person)))
            ->when($request->industry, fn($q) => $q->whereHas('industryCategory', fn($q) => $q->where('name', $request->industry)))
            ->when($request->country, fn($q) => $q->whereHas('country', fn($q) => $q->where('name', $request->country)))
            ->get();

        $pdf = PDF::loadView('client_database.partials.export_pdf', ['clients' => $clients]);

        return $pdf->download('clients_export.pdf');
    }
}
