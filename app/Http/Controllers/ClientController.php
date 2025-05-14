<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\IndustryCategory;
use App\Models\User;
use App\Models\Country;
use Yajra\DataTables\DataTables;

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

    public function store(Request $request)
    {
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
            'sales_person_id' => 'required|exists:users,id',
            'image_path' => 'nullable|image|max:2048', // Accept only image files
        ]);

        // Handle file upload if exists
        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('clients', 'public');
            $validated['image_path'] = $path;
        }

        // Save the client
        Client::create($validated);

        return redirect()->route('v1.client-database.list')->with('success', 'Client created successfully.');
    }


    public function update(Request $request,$id)
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
            'sales_person_id' => 'required|exists:users,id',
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

    public function list()
    {
        return view('client_database.list')->with('title', 'List of Client Database')->with('breadcrumb', ['Home', 'Client Database', 'List of Client Database']);

    }

    public function toggleStatus(Request $request)
    {
        $user = Client::find($request->id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        if ($request->action === 'deactivate') {
            $user->data_status = 0;
        } else {
            $user->data_status = 1;
        }
        $user->save();

        return response()->json(['success' => true, 'message' => 'Client deleted successfully']);
    }

    public function getClientsData()
    {
        $clients = Client::with(['industryCategory', 'country', 'salesPerson'])
        ->where('data_status',1);

        return DataTables::of($clients)
            ->addIndexColumn()
            ->addColumn('industry', fn($client) => $client->industryCategory->name ?? '-')
            ->addColumn('country', fn($client) => $client->country->name ?? '-')
            ->addColumn('sales_person', fn($client) => $client->salesPerson->name ?? '-')
            ->addColumn('image_path', function ($row) {
                    if (!empty($row->image_path)) {

                    return asset('storage/' . $row->image_path);
                    } else {
                        return asset('assets/img/photos/default.png');
                    }
                })
            ->addColumn('image_path1', function ($row) {
                    if (!empty($row->image_path)) {

                    return '<img src="' . asset('storage/' . $row->image_path). '" alt="User Image" width="50" height="50" class="rounded-circle">';
                    } else {
                        return '<img src="' . asset('assets/img/photos/default.png') . '" alt="User Image" width="50" height="50" class="rounded-circle">';
                    }
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('v1.client-database.edit', ['id' => $row->id]) . '" class="btn btn-primary btn-sm">Edit</a>';

                    if ($row->data_status != 0) {
                        $btn .= ' <a href="javascript:void(0)" class="confirm-action btn btn-danger btn-sm"
                                data-id="' . $row->id . '"
                                data-action="deactivate">Delete</a>';
                    } else {
                        $btn .= ' <a href="javascript:void(0)" class="confirm-action btn btn-success btn-sm"
                                data-id="' . $row->id . '"
                                data-action="activate">Activate</a>';
                    }

                    return $btn;
                })
                ->addColumn('quotation', fn($client) => 'Nil')
            ->make(true);
    }
}
