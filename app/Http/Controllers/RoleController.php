<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
//use Spatie\Permission\Models\Role;
use App\Models\Role;
//use Yajra\DataTables\DataTables;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-role|edit-role|delete-role', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-role', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-role', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-role', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        return view('role.list', ['user' => $user,
            'roles' => Role::with('permissions')->orderBy('id', 'DESC')->paginate(3),
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
            'name' => 'required|string|max:255',
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

        return view('role.show', compact('data', 'permissions'))->with('title', 'Role Detail')->with('breadcrumb', ['Home', 'Master Data', 'User Role Management', 'Role Detail']);
    }

    public function assignPermissions(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
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

        return view('role.edit', compact('role'))->with('title', 'Edit Role')->with('breadcrumb', ['Home', 'Master Data', 'User Role Management', 'Edit Role']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
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

        

        // If role is not in use, proceed with deletion
        if($role->roleStatus->data_status==2)
        {
            $role->roleStatus->data_status = 1;
        } else {
            $role->roleStatus->data_status = 2;
        }
        $role->roleStatus->save();

        return redirect()->route('v1.roles')->with('success', 'Role deleted successfully.');
    }

    public function getRoles(Request $request)
    {
        // Get the status parameter from the request
        $statusFilter = $request->input('status'); // Will be 'active', 'inactive', or null

        $query = Role::query();

        // Join with role_statuses table to filter by status
        // Ensure you have the roleStatus relationship defined in your Role model
        if ($statusFilter) {
            $query->whereHas('roleStatus', function ($q) use ($statusFilter) {
                $q->where('data_status', $statusFilter);
            });
        }
        // If no status filter is provided, you might want to default to 'active' or show all.
        // For this setup, if statusFilter is null, it will show all roles without a status condition.
        // If you want to explicitly only show active by default if no filter, add an 'else' condition here.

        return DataTables::eloquent($query)
            ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('v1.roles.edit', ['id' => $row->id]).'" class="edit btn btn-success btn-sm">Edit</a>';
                    $btn .= ' <a href="'.route('v1.roles.show', ['id' => $row->id]).'" class="edit btn btn-info btn-sm">Permission</a>';
                    if ($row->roleStatus->data_status == 1) { // <--- Condition to show delete button if status is 'inactive' (2)
                        $btn .= '<form class="d-inline-block me-3" action="'.route('v1.roles.destroy', $row->id).'" method="post">'; // Using d-inline-block for spacing
                        $btn .= '    ' . csrf_field(); // Laravel CSRF token helper
                        $btn .= '    ' . method_field('PUT'); // <--- FIX: Use DELETE method for destroy route (conventional)
                        $btn .= '    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to inactive this role?\')">Inactive</button>';
                        $btn .= '</form>';
                    }

                    if ($row->roleStatus->data_status == 2) { // <--- Condition to show delete button if status is 'inactive' (2)
                        $btn .= '<form class="d-inline-block me-2" action="'.route('v1.roles.destroy', $row->id).'" method="post">'; // Using d-inline-block for spacing
                        $btn .= '    ' . csrf_field(); // Laravel CSRF token helper
                        $btn .= '    ' . method_field('PUT'); // <--- FIX: Use DELETE method for destroy route (conventional)
                        $btn .= '    <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm(\'Are you sure you want to active this role?\')">Active</button>';
                        $btn .= '</form>';
                    }

                    return $btn;
                })
            ->rawColumns(['action'])
            ->make(true);

    }
    public function getRolesx(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::select('*')->with('roleStatus');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('v1.roles.edit', ['id' => $row->id]).'" class="edit btn btn-success btn-sm">Edit</a>';
                    $btn .= ' <a href="'.route('v1.roles.show', ['id' => $row->id]).'" class="edit btn btn-info btn-sm">Permission</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
