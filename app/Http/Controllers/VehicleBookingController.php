<?php

namespace App\Http\Controllers;

use App\Models\JobAssignment;
use App\Models\Vehicle;
use App\Models\VehicleBooking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class VehicleBookingController extends Controller
{
    public function index()
    {
        return view('vehicle_booking.index')->with('title', 'Vehicle Booking')->with('breadcrumb', ['Home', 'Staff Task', 'Vehicle Booking']);
    }

    public function edit($id)
    {
        $now = Carbon::now();
        $vehicles = Vehicle::all();

        // Retrieve the existing vehicle booking record
        $booking = VehicleBooking::findOrFail($id);

        $jobs = JobAssignment::select('id', 'scope_of_work')
            ->where('job_assignments.user_id', auth()->user()->id)
            ->whereNot('job_assignments.job_status', 3)
            ->where(function ($query) use ($now) {
                $query->where('job_assignments.start_at', '>=', $now) // Job starts today or earlier
                    ->orWhereDate('job_assignments.start_at', $now); // Exact match with today's date
            })
            ->orderBy('job_assignments.created_at', 'DESC')
            ->get();

        return view('vehicle_booking.edit', compact('vehicles', 'jobs', 'booking'))
            ->with('title', 'Edit Vehicle Booking')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Vehicle Booking', 'Edit Vehicle Booking']);
    }


    public function create()
    {
        $now = Carbon::now();
        $vehicles = Vehicle::all();
        $jobs = JobAssignment::select('id', 'scope_of_work')
            ->where('job_assignments.user_id', auth()->user()->id)
            ->whereNot('job_assignments.job_status', 3)
            ->where(function ($query) use ($now) {
                $query->where('job_assignments.start_at', '>=', $now) // Job starts today or earlier
                    //->where('job_assignments.end_at', '>=', $now) // Job ends today or later
                    ->orWhereDate('job_assignments.start_at', $now); // Exact match with today's date
                //->orWhereDate('job_assignments.end_at', $now); // Exact match with today's date
            })
            ->orderBy('job_assignments.created_at', 'DESC')
            ->get();
        return view('vehicle_booking.form', compact('vehicles', 'jobs'))->with('title', 'Create a Vehicle Booking')->with('breadcrumb', ['Home', 'Staff Task', 'Vehicle Booking', 'Create a Vehicle Booking']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_at' => 'required|date|after_or_equal:today',
            'end_at' => 'required|date|after_or_equal:start_at',
            'purposes' => 'required|string',
            'job_assignment_id' => 'nullable|exists:job_assignments,id',
        ]);

        // Find the existing booking
        $booking = VehicleBooking::findOrFail($id);

        // Update the booking details
        $booking->update([
            'vehicle_id' => $validated['vehicle_id'],
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'purposes' => $validated['purposes'],
            'job_assignment_id' => $validated['job_assignment_id'],
        ]);

        return redirect()->route('v1.vehicle-bookings.list')
            ->with('success', 'Vehicle booking updated successfully.');
    }


    public function store(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_at' => 'required|date|after_or_equal:today',
            'end_at' => 'required|date|after:start_at',
            'purposes' => 'required|string',
            'job_assignment_id' => 'nullable|exists:job_assignments,id'
        ]);

        // Create a new vehicle booking record
        $booking = VehicleBooking::create([
            'vehicle_id' => $validated['vehicle_id'],
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'purposes' => $validated['purposes'],
            'job_assignment_id' => $validated['job_assignment_id'],
            'created_by' => auth()->id(), // Assign booking to logged-in user
        ]);

        // Redirect back with success message
        return redirect()->route('v1.vehicle-bookings.list')->with('success', 'Vehicle booked successfully.');
    }


    public function list()
    {
        return view('vehicle_booking.list')->with('title', 'Vehicle Booking List')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Vehicle Booking List']);
    }

    public function cancel(Request $request, $id)
    {
        $now = Carbon::now();
        $booking = VehicleBooking::findOrFail($id);
        $booking->update(['end_at' => $now]); // OR $booking->delete();
        return redirect()->route('v1.vehicle-bookings.list')->with('success', 'Vehicle Bookings canceled successfully.');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $now = Carbon::now(); // Get current date & time
            // Filter bookings where end_at is today or in the future
            $data = VehicleBooking::where('end_at', '>=', $now)->select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->vehicle->name;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group" aria-label="Basic example"><a href="' . route('v1.vehicle-bookings.edit', ['id' => $row->id]) . '" class="edit btn btn-success btn-sm">Edit</a>';
                    $btn1 = '<a href="javascript:void(0);"
                class="btn btn-danger btn-sm cancel-booking"
                data-id="' . $row->id . '"
                data-bs-toggle="modal"
                data-bs-target="#cancelConfirmModal">Cancel</a>';
                    return $btn;
                })
                ->rawColumns(['name', 'action'])
                ->make(true);
        }
    }




}
