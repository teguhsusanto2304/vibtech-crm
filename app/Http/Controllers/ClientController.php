<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\IndustryCategory;
use App\Models\User;
use App\Models\Country;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('clients')->where(function ($query) use ($request) {
                    return $query->where('company', $request->company);
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
            //'sales_person_id' => 'required|exists:users,id',
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

    public function list()
    {
        return view('client_database.list', [
            'industries' => IndustryCategory::all(),
            'countries' => Country::all(),
            'salesPersons' => User::all()
        ])->with('title', 'List of Client Database')->with('breadcrumb', ['Home', 'Client Database', 'List of Client Database']);

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

    public function getClientsData(Request $request)
    {
        $clients = Client::with(['industryCategory', 'country', 'salesPerson'])
            ->where('data_status', 1);
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
                $btn = '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><button class="btn btn-info btn-sm view-client" data-id="' . $row->id . '">View</button>';
                $btn .= '<a href="' . route('v1.client-database.edit', ['id' => $row->id]) . '" class="btn btn-primary btn-sm">Edit</a>';

                if ($row->data_status != 0) {
                    $btn .= ' <a href="javascript:void(0)" class="confirm-action btn btn-danger btn-sm"
                                data-id="' . $row->id . '"
                                data-action="deactivate">Delete</a></div>';
                } else {
                    $btn .= ' <a href="javascript:void(0)" class="confirm-action btn btn-success btn-sm"
                                data-id="' . $row->id . '"
                                data-action="activate">Activate</a>';
                }

                return $btn;
            })
            ->addColumn('quotation', fn($client) => 'Nil')
            ->addColumn('created_on', fn($client) => $client->created_at->format('d M Y') ?? '-')
            ->addColumn('updated_on', function ($client) {
                if ($client->created_at != $client->updated_at) {
                    return $client->updated_at->format('d M Y');
                } else {
                    return '';
                }
            })
            ->make(true);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');

        // Open and read the CSV
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data = array_combine($header, $row);

                // Store data
                Client::create([
                    'name' => $data['name'] ?? null,
                    'position' => $data['position'] ?? null,
                    'email' => $data['email'] ?? null,
                    'office_number' => $data['office_number'] ?? null,
                    'mobile_number' => $data['mobile_number'] ?? null,
                    'job_title' => $data['job_title'] ?? null,
                    'company' => $data['company'] ?? null,
                    'industry_category_id' => IndustryCategory::where('name', $data['industry_category'])->value('id'),
                    'country_id' => Country::where('name', $data['country'])->value('id'),
                    'sales_person_id' => auth()->id(), // or $data['sales_person_id']
                ]);
            }

            fclose($handle);
        }

        return redirect()->route('v1.client-database.list')->with('success', 'CSV imported successfully!');
    }

    public function getClientDetail($id)
    {
        $client = Client::with(['industryCategory', 'country', 'salesPerson'])->findOrFail($id);

        return view('client_database.partials.detail', compact('client'));
    }

}
