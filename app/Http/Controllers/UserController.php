<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\PositionLevel;
use app\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function index()
    {
        return view('user.list')->with('title', 'User Management')->with('breadcrumb', ['Home', 'Master Data', 'User Management']);
    }

    public function create()
    {
        $roles = Role::all();
        $departments = Department::all();
        $position_levels = PositionLevel::all();

        return view('user.form', compact('roles', 'departments', 'position_levels'))->with('title', 'Create a New User')->with('breadcrumb', ['Home', 'Master Data', 'User Management', 'Creat a New User']);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'user_number' => 'required|string|unique:users,user_number|max:50',
            'department_id' => 'required',
            'location' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'joined_at' => 'nullable|date',
            'dob' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email|max:255',
            'path_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'role_id' => 'required'
        ]);

        $validatedData['password'] = 'password';
        try {
            if ($request->hasFile('path_image')) {
                $image = $request->file('path_image');
                $imageName = time() . '_' . $image->getClientOriginalName(); // Unique image name
                $imagePath = 'assets/img/photos/' . $imageName; // dev  Define the path
                // $imagePath = 'public_html/crm/assets/img/photos/' . $imageName; //dev  Define the path

                // Move the image to the public folder
                $image->move(public_path('assets/img/photos'), $imageName);

                $validatedData['path_image'] = $imagePath; // Save path in the database
            }

            // Create a new user
            $user = User::create($validatedData);

            $user->assignRole($request->input('role_id'));
            Artisan::call('permission:cache-reset');

            // Return response
            return redirect()->route('v1.users')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function edit($emp_id)
    {
        $emp = User::findOrFail($emp_id);
        $roles = Role::all();
        $departments = Department::all();
        $position_levels = PositionLevel::all();

        return view('user.edit', compact('emp', 'roles', 'departments', 'position_levels'))->with('title', 'Edit a User')->with('breadcrumb', ['Home', 'Master Data', 'User Management', 'Edit a User']);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'user_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'user_number')->ignore($user->id), // Ignore current user
            ],
            'email' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'email')->ignore($user->id), // Ignore current user
            ],
            'department_id' => 'required',
            '2nd_department_id' => 'nullable',
            'location' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'position_level_id' => 'required',
            'joined_at' => 'nullable|date',
            'dob' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'path_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'role_id' => 'required',
        ]);

        // Handle Image Upload
        if ($request->hasFile('path_image')) {
            if ($user->path_image) {
                Storage::delete(public_path($user->path_image));
            }

            $image = $request->file('path_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = 'assets/img/photos/' . $imageName;
            $image->move(public_path('assets/img/photos'), $imageName);
            $validatedData['path_image'] = $imagePath;
        }

        $user->update($validatedData);
        $role = Role::find($request->input('role_id')); // Get role by ID

        if ($role) {
            DB::table('model_has_roles')
                ->where('model_id', $id)
                ->where('model_type', 'App\Models\User')
                ->delete(); // Remove old role
            DB::table('model_has_roles')->insert([
                'role_id' => $request->input('role_id'),
                'model_id' => $id,
                'model_type' => 'App\Models\User',
            ]); // Set new role
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('permission:cache-reset');
        } else {
            return back()->with('error', 'Invalid role selected.');
        }

        return redirect()->route('v1.users')->with('success', 'User updated successfully.');
    }

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('path_image', function ($row) {
                    // $profileUrl = asset('assets/images/default.png'); // Default image

                    // if (!empty($row->profile_picture)) {
                    // $profileUrl = asset('storage/' . $row->path_image); // If stored in `storage/app/public/`
                    // }
                    if (!empty($row->path_image)) {

                        return '<img src="' . asset($row->path_image) . '" alt="User Image" width="50" height="50" class="rounded-circle">';
                    } else {
                        return '<img src="' . asset('assets/img/photos/default.png') . '" alt="User Image" width="50" height="50" class="rounded-circle">';
                    }
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('v1.users.edit', ['emp_id' => $row->id]) . '" class="edit btn btn-primary btn-sm">Edit</a>';

                    if ($row->user_status != 0) {
                        $btn .= ' <a href="javascript:void(0)" class="confirm-action btn btn-danger btn-sm"
                                data-id="' . $row->id . '"
                                data-action="deactivate">Deactivate</a>';
                    } else {
                        $btn .= ' <a href="javascript:void(0)" class="confirm-action btn btn-success btn-sm"
                                data-id="' . $row->id . '"
                                data-action="activate">Activate</a>';
                    }

                    return $btn;
                })
                ->addColumn('dept', function ($row) {
                    
                        $result = $row->dept->name ?? 'No Department';

                    return $result;
                })
                ->addColumn('role', function($row) {
                    foreach($row->getRoleNames() as $role){
                        return $role;
                    }
                })
                ->rawColumns(['path_image', 'action', 'dept','role'])
                ->make(true);
        }
    }

    public function toggleStatus(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        if ($request->action === 'deactivate') {
            $user->user_status = 0;
            $wasDeactivated = true;
        } else {
            $user->user_status = 1;
            $wasDeactivated = false;
        }
        $user->password = 'password';
        $user->save();

        // 🔥 Call FastAPI to delete user if deactivated
        if ($wasDeactivated) {
            $fastApiUrl = env('CHAT_URL')."/users/by-username/{$user->name}";

            try {
                $response = Http::delete($fastApiUrl);

                if (!$response->successful()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User deactivated in Laravel, but failed to delete in FastAPI',
                        'fastapi_error' => $response->body()
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'User deactivated in Laravel, but FastAPI request failed ',
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'User status updated successfully']);
    }

    public function getOfflineUsers(Request $request)
    {
        $data = User::select('name')->get();

        return response()->json($data);
    }

}
