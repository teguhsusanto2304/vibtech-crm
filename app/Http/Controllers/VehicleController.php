<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class VehicleController extends Controller
{
    public function list(Request $request)
    {
        return view('vehicle.list')->with('title', 'Vehicle List')->with('breadcrumb', ['Home', 'Master Data', 'Vehicle List']);
    }
    public function create(Request $request)
    {
        return view('vehicle.form')->with('title', 'Vehicles')->with('breadcrumb', ['Home', 'Staff Task', 'Vehicles']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'path_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('path_image')) {
            $image = $request->file('path_image');
            $imageName = time() . '_' . $image->getClientOriginalName(); // Unique image name
            $imagePath = 'assets/img/cars/' . $imageName; //dev  Define the path
            //$imagePath = 'public_html/crm/assets/img/photos/' . $imageName; //dev  Define the path

            // Move the image to the public folder
            $image->move(public_path('assets/img/cars'), $imageName);

            $validatedData['path_image'] = $imagePath; // Save path in the database
        }

        Vehicle::create([
            'name' => $request->name,
            'path_image' => $imagePath,
        ]);

        return redirect()->route('v1.vehicles')->with('success', 'Vehicle added successfully.');
    }

    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return view('vehicle.edit',compact('vehicle'))->with('title', 'Edit Vehicles')->with('breadcrumb', ['Home', 'Master Data', 'Vehicles']);
    }



    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'path_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $vehicle = Vehicle::findOrFail($id);

        $imagePath = null;
        if ($request->hasFile('path_image')) {
            $image = $request->file('path_image');
            $imageName = time() . '_' . $image->getClientOriginalName(); // Unique image name
            $imagePath = 'assets/img/cars/' . $imageName; //dev  Define the path
            //$image->move(base_path('../public_html/crm/assets/img/cars/'), $imageName);

            // Move the image to the public folder
            $image->move(public_path('assets/img/cars'), $imageName);
            $vehicle->path_image = $imagePath;
        }

        $vehicle->name = $request->name;
        $vehicle->save();

        return redirect()->route('v1.vehicles.list')->with('success', 'Vehicle updated successfully.');
    }

    public function delete(Request $request, $id)
    {
        $now = Carbon::now();
        $booking = Vehicle::findOrFail($id);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        $booking->data_status = 1;
        $booking->save();
        return response()->json(['message' => 'Vehicle deleted successfully']);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $now = Carbon::now(); // Get current date & time
            // Filter bookings where end_at is today or in the future
            $data = Vehicle::select('*')->whereNot('data_status',1);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group" aria-label="Basic example"><a href="' . route('v1.vehicles.edit', ['id' => $row->id]) . '" class="edit btn btn-success btn-sm">Edit</a>';
                    $btn .= '<a href="javascript:void(0);"
                class="btn btn-danger btn-sm delete-vehicle"
                data-id="' . $row->id . '"
                data-bs-toggle="modal"
                data-bs-target="#cancelConfirmModal">Delete</a>';
                    return $btn;
                })
                ->addColumn('path_image', function ($row) {
                    if (!empty($row->path_image)) {

                    return '<img src="' . asset($row->path_image) . '" alt="User Image" width="150px" height="150px" >';
                    } else {
                        return '<img src="' . asset('assets/img/cars/default.png') . '" alt="User Image" width="50" height="50" >';
                    }
                })
                ->rawColumns(['path_image','action'])
                ->make(true);
        }
    }

    public function getCarImages()
    {
        $cars = Vehicle::select('id', 'path_image')->get(); // Adjust fields based on your table
        return response()->json($cars);
    }
}
