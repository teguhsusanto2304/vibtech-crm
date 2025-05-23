<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-department|edit-department|delete-department', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-department', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-department', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-department', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('department.list', ['departments' => Department::orderBy('id', 'DESC')->paginate(3),
        ])->with('title', 'Department')->with('breadcrumb', ['Home', 'Master Data', 'Department']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('department.form')->with('title', 'Create a New Department')->with('breadcrumb', ['Home', 'Master Data', 'Department', 'Creat a New Department']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        Department::create($validatedData);

        // Return response
        return redirect()->route('v1.departments')->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getDepartments(Request $request)
    {
        if ($request->ajax()) {
            $data = Department::select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('v1.users.edit', ['emp_id' => $row->id]).'" class="edit btn btn-success btn-sm">Edit</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
