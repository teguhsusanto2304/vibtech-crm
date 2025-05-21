<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientRequest;
use App\Models\IndustryCategory;
use App\Models\User;
use App\Models\Country;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

use function PHPUnit\Framework\isEmpty;

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
        return view('client_database.index')->with('title', 'Client Database')->with('breadcrumb', ['Home', 'Marketing', 'Client Database']);
    }

    public function create()
    {
        return view('client_database.form', [
            'industries' => IndustryCategory::all(),
            'countries' => Country::all(),
            'salesPeople' => User::all()
        ])->with('title', 'Client Database')->with('breadcrumb', ['Home', 'Marketing', 'Create a Client Data']);
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
            'breadcrumb' => ['Home', 'Marketing', 'Edit a Client Data'],
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
            'breadcrumb' => ['Home', 'Marketing', 'Preview a Client Data'],
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
            //'sales_person_id' => 'required|exists:users,id',
            'image_path' => 'nullable|image|max:2048', // Accept only image files
        ]);

        // Handle file upload if exists
        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('clients', 'public');
            $validated['image_path'] = $path;
        }

        // Save the client
        $client->update($validated);

        return redirect()->route('v1.client-database.list')->with('success', 'Client updated successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'contact_for_id' => 'required|exists:users,id',
        ]);

        $file = $request->file('csv_file');

        // Open and read the CSV
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data = array_combine($header, $row);
                if (!empty($data['name'])) {
                    // Store data
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
                        //\\'industry_category_id' => IndustryCategory::where('name', $data['industry_category'])->value('id'),
                        //'country_id' => Country::where('name', $data['country'])->value('id'),
                        //'sales_person_id' => auth()->id(), // or $data['sales_person_id']
                    ]);
                }
            }

            fclose($handle);
        }

        return redirect()->route('v1.client-database.list')->with('success', 'CSV imported successfully!');
    }

    public function updateRequest(Request $request, $id)
    {
        DB::beginTransaction();
        $clientReq = ClientRequest::findOrFail($id);
        dd($clientReq);
        $clientReq->data_status = 3;
        $clientReq->save();

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
        // Save the client
        $client->update($validated);

        DB::commit();
        return redirect()->route('v1.client-database.request-list')->with('success', 'Client updated successfully.');
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
            'breadcrumb' => ['Home', 'Marketing', 'Edit a Client Data'],
        ]);
    }

    public function list()
    {
        return view('client_database.list', [
            'industries' => IndustryCategory::all(),
            'countries' => Country::all(),
            'salesPersons' => User::all()
        ])->with('title', 'List of Client Database')->with('breadcrumb', ['Home', 'Client Database', 'List of Client Database']);

    }

    public function assignmentList()
    {
        return view('client_database.assignment_list', [
            'industries' => IndustryCategory::all(),
            'countries' => Country::all(),
            'salesPersons' => User::all()
        ])->with('title', 'List of Salesperson Assignment')->with('breadcrumb', ['Home', 'Client Database', 'List of Salesperson Assignment']);

    }

    public function updateRequestList()
    {
        return view('client_database.request.list', [
            'industries' => IndustryCategory::all(),
            'countries' => Country::all(),
            'salesPersons' => User::all()
        ])->with('title', 'List of Edit/Delete Request')->with('breadcrumb', ['Home', 'Client Database', 'List of Edit/Delete Request']);

    }

    public function toggleStatus(Request $request)
    {
        $user = Client::find($request->id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        $user->data_status = 0;

        $user->save();

        return response()->json(['success' => true, 'message' => 'Client deleted successfully']);
    }

    public function assignmentSalesperson(Request $request)
    {
        DB::beginTransaction();

        try {
            $client = Client::findOrFail($request->client_id);

            $validated = $request->validate([
                'sales_person_id' => 'required',
            ]);
            $validated['is_editable'] = $request->has('is_editable') ? 1 : 0;
            $validated['updated_by'] = auth()->user()->id;
            $validated['updated_at'] = date('Y-m-d H:i:s');
            // Save the client
            $client->update($validated);
            DB::commit();
            return redirect()->route('v1.client-database.assignment-salesperson.list')->with('success', 'Salesperson has assigned successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Rollback transaction on validation error
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Rollback transaction on any other exception
            DB::rollBack();
            // Log the error for debugging
            //\Log::error('Error updating client: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Failed to update client. Please try again.')->withInput();
        }
    }

    public function deleteFromRequest(Request $request)
    {
        $clientRequest = ClientRequest::find($request->id);
        $client = Client::find($clientRequest->client_id);

        if (!$clientRequest) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        $clientRequest->data_status = 4;
        $clientRequest->save();

        $client->data_status = 0;
        $client->save();

        return response()->json(['success' => true, 'message' => 'Client request deleted successfully']);
    }

    public function getClientsData(Request $request)
    {
        $clients = Client::leftJoin('industry_categories', 'clients.industry_category_id', '=', 'industry_categories.id')
            ->leftJoin('countries', 'clients.country_id', '=', 'countries.id')
            ->leftJoin('users', 'clients.sales_person_id', '=', 'users.id')
            ->join('users as created', 'clients.created_id', '=', 'created.id')
            ->leftJoin('users as updated', 'clients.updated_id', '=', 'updated.id')
            ->leftJoin('client_requests', 'clients.id', '=', 'client_requests.client_id')
            ->with(['industryCategory', 'country', 'salesPerson', 'createdBy', 'updatedBy'])
            ->where('clients.data_status', 1)
            ->select('clients.*');
        if ($request->filled('sales_person')) {
            $clients->whereHas('salesPerson', function ($q) use ($request) {
                $q->where('name', $request->sales_person);
            });
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
                    //return '<svg fill="#000000" width="80px" height="80px" viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:none;}</style></defs><title>no-image</title><path d="M30,3.4141,28.5859,2,2,28.5859,3.4141,30l2-2H26a2.0027,2.0027,0,0,0,2-2V5.4141ZM26,26H7.4141l7.7929-7.793,2.3788,2.3787a2,2,0,0,0,2.8284,0L22,19l4,3.9973Zm0-5.8318-2.5858-2.5859a2,2,0,0,0-2.8284,0L19,19.1682l-2.377-2.3771L26,7.4141Z"/><path d="M6,22V19l5-4.9966,1.3733,1.3733,1.4159-1.416-1.375-1.375a2,2,0,0,0-2.8284,0L6,16.1716V6H22V4H6A2.002,2.002,0,0,0,4,6V22Z"/><rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1" width="32" height="32"/></svg>';
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
                $btnEdit = "";
                $btn = '<div class="btn-group btn-group-vertical" role="group" aria-label="Basic mixed styles example"><button class="btn btn-info btn-sm view-client" data-id="' . $row->id . '">View</button>';

                $btnDelete = '';
                $btnEdit = '';
                if ($row->sales_person_id == auth()->user()->id) {
                    $delete = $row->clientRequests
                        ->filter(function ($req) {
                            return $req->data_status == 2;
                        })
                        ->sortByDesc('created_at') // Urutkan berdasarkan waktu pembuatan terbaru
                        ->first();
                        if (is_null($delete)) {
                            $btnDelete .= '<button class="btn btn-danger btn-sm view-client-delete" data-id="' . $row->id . '">Request to Delete</button></div>';
                        } else {
                            if (is_null($delete->approved_by)) {
                                $btnDelete .= '<button class="btn btn-danger btn-sm" disabled>Waiting Response</button>';
                            } else {
                                 $btnDelete .= '<button class="btn btn-danger btn-sm" disabled>Delete</button>';
                            }
                        }

                        $edit = $row->clientRequests
                        ->filter(function ($req) {
                            return $req->data_status == 1;
                        })
                        ->sortByDesc('created_at') // Urutkan berdasarkan waktu pembuatan terbaru
                        ->first();
                        if (is_null($edit)) {
                            $btnEdit .= '<button class="btn btn-primary btn-sm view-client-delete" data-id="' . $row->id . '">Request to Edit</button>';
                        } else {
                            if (is_null($edit->approved_by)) {
                                $btnEdit .= '<button class="btn btn-primary btn-sm" disabled>Waiting Response</button>';
                            } else {
                                 $btnEdit .= '<button class="btn btn-primary btn-sm" disabled>Edit</button>';
                            }
                        }

                }
                return $btn . $btnEdit.$btnDelete;
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
        $clients = ClientRequest::leftJoin('industry_categories', 'client_requests.industry_category_id', '=', 'industry_categories.id')
            ->leftJoin('countries', 'client_requests.country_id', '=', 'countries.id')
            ->leftJoin('users', 'client_requests.sales_person_id', '=', 'users.id')
            ->with(['industryCategory', 'country', 'salesPerson'])
            ->select('client_requests.*');
        if ($request->filled('sales_person')) {
            $clients->whereHas('salesPerson', function ($q) use ($request) {
                $q->where('name', $request->sales_person);
            });
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
                    //return '<svg fill="#000000" width="80px" height="80px" viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:none;}</style></defs><title>no-image</title><path d="M30,3.4141,28.5859,2,2,28.5859,3.4141,30l2-2H26a2.0027,2.0027,0,0,0,2-2V5.4141ZM26,26H7.4141l7.7929-7.793,2.3788,2.3787a2,2,0,0,0,2.8284,0L22,19l4,3.9973Zm0-5.8318-2.5858-2.5859a2,2,0,0,0-2.8284,0L19,19.1682l-2.377-2.3771L26,7.4141Z"/><path d="M6,22V19l5-4.9966,1.3733,1.3733,1.4159-1.416-1.375-1.375a2,2,0,0,0-2.8284,0L6,16.1716V6H22V4H6A2.002,2.002,0,0,0,4,6V22Z"/><rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1" width="32" height="32"/></svg>';
                }
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><button class="btn btn-info btn-sm view-client" data-id="' . $row->id . '">View</button>';

                if ($row->data_status == 1) {
                    $btn .= '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a class="btn btn-success btn-sm" href="' . route('v1.client-database.preview', $row->id) . '">Preview</button>';
                } else if ($row->data_status == 2) {
                    $btn .= ' <a href="javascript:void(0)" class="confirm-action btn btn-danger btn-sm">Delete</a>';
                }

                return $btn;
            })
            ->addColumn('quotation', fn($client) => 'Nil')
            ->addColumn('created_on', fn($client) => $client->created_at->format('d M Y'))
            ->addColumn('created_by', function ($client) {
                if ($client->created_at) {
                    return $client->updated_at->format('d M Y') . '<p><span class="badge bg-info"><small>' . $client->user->name . '</small></span>';
                } else {
                    return '';
                }
            })
            ->addColumn('updated_on', function ($client) {
                if ($client->created_at != $client->updated_at) {
                    return $client->updated_at->format('d M Y');
                } else {
                    return '';
                }
            })
            ->addColumn('request_status', function ($client) {
                if ($client->data_status == 1) {
                    return '<span class="badge bg-primary">Edit</span>';
                } else if ($client->data_status == 4) {
                    return '<span class="badge bg-danger">Has Deleted</span>';
                } else if ($client->data_status == 3) {
                    return '<span class="badge bg-success">Has Updated</span>';
                } else {
                    return '<span class="badge bg-warning">Delete</span>';
                }
            })
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
            ->select('clients.*');
        if ($request->filled('sales_person')) {
            $clients->whereHas('salesPerson', function ($q) use ($request) {
                $q->where('name', $request->sales_person);
            });
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
                    //return '<svg fill="#000000" width="80px" height="80px" viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:none;}</style></defs><title>no-image</title><path d="M30,3.4141,28.5859,2,2,28.5859,3.4141,30l2-2H26a2.0027,2.0027,0,0,0,2-2V5.4141ZM26,26H7.4141l7.7929-7.793,2.3788,2.3787a2,2,0,0,0,2.8284,0L22,19l4,3.9973Zm0-5.8318-2.5858-2.5859a2,2,0,0,0-2.8284,0L19,19.1682l-2.377-2.3771L26,7.4141Z"/><path d="M6,22V19l5-4.9966,1.3733,1.3733,1.4159-1.416-1.375-1.375a2,2,0,0,0-2.8284,0L6,16.1716V6H22V4H6A2.002,2.002,0,0,0,4,6V22Z"/><rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1" width="32" height="32"/></svg>';
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
        $client_req = new ClientRequest();
        $client_req->client_id = $request->input('client_id');
        $client_req->remark = $request->input('remark');
        $client_req->data_status = $request->input('status') == 'delete' ? 2 : 1;
        $client_req->created_by = auth()->user()->id;
        $client_req->save();
        $msg = $request->input('status') == 'delete' ? 'Client delete requested successfully!' : 'Client edit requested successfully!';

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
            'Updated On'
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
