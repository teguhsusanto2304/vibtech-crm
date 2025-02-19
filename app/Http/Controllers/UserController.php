<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use app\Models\User;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('user.list', compact('user'))->with('title', 'User Management')->with('breadcrumb', ['Home', 'Master Data', 'User Management']);
    }

    public function create()
    {
        $user = auth()->user();
        return view('user.form', compact('user'))->with('title', 'Create a New User')->with('breadcrumb', ['Home', 'Master Data', 'User Management', 'Creat a New User']);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'user_number' => 'required|string|unique:users,user_number|max:50',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'joined_at' => 'nullable|date',
            'dob' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email|max:255',
            'path_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        $validatedData['password'] = 'password';
        try {
            if ($request->hasFile('path_image')) {
                $image = $request->file('path_image');
                $imageName = time() . '_' . $image->getClientOriginalName(); // Unique image name
                $imagePath = 'assets/img/photos/' . $imageName; // Define the path

                // Move the image to the public folder
                $image->move(public_path('assets/img/photos'), $imageName);

                $validatedData['path_image'] = $imageName; // Save path in the database
            }

            // Create a new user
            $user = User::create($validatedData);

            // Return response
            return redirect()->route('v1.users')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function edit($emp_id)
    {
        $user = auth()->user();
        $emp = User::findOrFail($emp_id);
        return view('user.edit', compact('user','emp'))->with('title', 'Edit a User')->with('breadcrumb', ['Home', 'Master Data', 'User Management', 'Edit a User']);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'user_number' => 'required|string|unique:users,user_number,' . $id . '|max:50',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'joined_at' => 'nullable|date',
            'dob' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $id . '|max:255',
            'path_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
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

        return redirect()->route('v1.users')->with('success', 'User updated successfully.');
    }

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('v1.users.edit',['emp_id'=>$row->id]).'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
