<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-permission|edit-role|delete-role', ['only' => ['index','show']]);
        $this->middleware('permission:create-permission', ['only' => ['create','store']]);
        $this->middleware('permission:edit-permission', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-permission', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('permission.list', ['permissions' => Permission::orderBy('id', 'DESC')->paginate(3)
        ])->with('title', 'Permission')->with('breadcrumb', ['Home', 'Master Data', 'Permission']);
    }

    public function create()
    {
        return view('permission.form')->with('title', 'Create a New Permission')->with('breadcrumb', ['Home', 'Master Data', 'Permission', 'Creat a New Permission']);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255'
        ]);
        Permission::create($validatedData);

            // Return response
        return redirect()->route('v1.permissions')->with('success', 'Permission created successfully.');
    }

    public function getPermissions(Request $request)
    {
        if ($request->ajax()) {
            $data = Permission::select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('v1.users.edit',['emp_id'=>$row->id]).'" class="edit btn btn-success btn-sm">Edit</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
