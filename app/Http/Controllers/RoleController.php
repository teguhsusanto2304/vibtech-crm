<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-role|edit-role|delete-role', ['only' => ['index','show']]);
        $this->middleware('permission:create-role', ['only' => ['create','store']]);
        $this->middleware('permission:edit-role', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-role', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        return view('role.list', ['user'=>$user,
            'roles' => Role::with('permissions')->orderBy('id', 'DESC')->paginate(3)
        ])->with('title', 'User Role Management')->with('breadcrumb', ['Home', 'Master Data', 'User Role Management']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('role.form')->with('title', 'Create a New Role')->with('breadcrumb', ['Home', 'Master Data', 'User Role Management', 'Creat a New Role']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255'
        ]);
        Role::create($validatedData);

            // Return response
        return redirect()->route('v1.roles')->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Role::findOrFail($id);
        $permissions = Permission::all();
        return view('role.show',compact('data','permissions'))->with('title', 'Role Detail')->with('breadcrumb', ['Home', 'Master Data', 'User Role Management', 'Role Detail']);
    }

    public function assignPermissions(Request $request)
{
    $request->validate([
        'id' => 'required',
        'permissions' => 'array',
        'permissions.*' => 'exists:permissions,id'
    ]);

    // Find the role by name
    $role = Role::findOrFail($request->input('id'));

    // Sync permissions (removes all previous and assigns new)
    $permissionNames = Permission::whereIn('id', $request->permissions ?? [])
                                ->pluck('name')
                                ->toArray();

    // Sync permissions with correct names
    $role->syncPermissions($permissionNames);

    return redirect()->route('v1.roles')->with('success', 'Permissions assigned successfully!');
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        return view('role.edit',compact('role'))->with('title', 'Edit Role')->with('breadcrumb', ['Home', 'Master Data', 'User Role Management', 'Edit Role']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $role = Role::findOrFail($id);

        // Update role name
        $role->update([
            'name' => $request->name,
        ]);

        return redirect()->route('v1.roles')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);

    // Check if role exists in role_has_permissions, model_has_roles, or users table
    $roleUsed = DB::table('role_has_permissions')->where('role_id', $id)->exists() ||
                DB::table('model_has_roles')->where('role_id', $id)->exists() ||
                DB::table('users')->where('role_id', $id)->exists(); // Adjust column name if different

    if ($roleUsed) {
        return redirect()->back()->with('error', 'Cannot delete role because it is still assigned.');
    }

    // If role is not in use, proceed with deletion
    //$role->delete();

    return redirect()->route('v1.roles')->with('success', 'Role deleted successfully.');
    }

    public function getRoles(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('v1.roles.edit',['id'=>$row->id]).'" class="edit btn btn-success btn-sm">Edit</a>';
                    $btn .= ' <a href="'.route('v1.roles.show',['id'=>$row->id]).'" class="edit btn btn-info btn-sm">Permission</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
