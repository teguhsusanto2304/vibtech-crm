<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function list(Request $request)
    {
        return view('vehicle.list')->with('title', 'Vehicles')->with('breadcrumb', ['Home', 'Staff Task', 'Vehicles']);
    }
    public function create(Request $request)
    {
        return view('vehicle.list')->with('title', 'Vehicles')->with('breadcrumb', ['Home', 'Staff Task', 'Vehicles']);
    }

    public function store(Request $request)
    {
        return view('vehicle.list')->with('title', 'Vehicles')->with('breadcrumb', ['Home', 'Staff Task', 'Vehicles']);
    }

    public function edit(Request $request)
    {
        return view('vehicle.list')->with('title', 'Vehicles')->with('breadcrumb', ['Home', 'Staff Task', 'Vehicles']);
    }

    public function update(Request $request)
    {
        return view('vehicle.list')->with('title', 'Vehicles')->with('breadcrumb', ['Home', 'Staff Task', 'Vehicles']);
    }

    public function getCarImages()
    {
        $cars = Vehicle::select('id', 'path_image')->get(); // Adjust fields based on your table
        return response()->json($cars);
    }
}
