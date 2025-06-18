<?php

namespace App\Http\Controllers;

use App\Models\JobAssignment;
use App\Models\Vehicle;
use App\Models\VehicleBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class VehicleBookingController extends Controller
{
    public function index()
    {
        return view('vehicle_booking.index')->with('title', 'Vehicle Booking')->with('breadcrumb', ['Home', 'Staff Task', 'Vehicle Booking']);
    }

    public function history()
    {
        return view('vehicle_booking.history')->with('title', 'Vehicle Booking History')->with('breadcrumb', ['Home', 'Staff Task', 'Vehicle Booking', 'Vehicle Booking History']);
    }

    public function edit($id)
    {
        $now = Carbon::now();
        $vehicles = Vehicle::all();

        // Retrieve the existing vehicle booking record
        $booking = VehicleBooking::findOrFail($id);

        $jobs = JobAssignment::select('job_assignments.id', 'job_assignments.job_type', 'job_assignments.job_record_id')
            ->join('job_assignment_personnels', 'job_assignments.id', '=', 'job_assignment_personnels.job_assignment_id')
            ->where('job_assignment_personnels.user_id', auth()->user()->id)
            ->where('job_assignment_personnels.is_booker', 1)
            ->whereNot('job_assignments.job_status', 3)
            ->where(function ($query) use ($now) {
                $query->where('job_assignments.start_at', '>=', $now) // Job starts today or earlier
                    // ->where('job_assignments.end_at', '>=', $now) // Job ends today or later
                    ->orWhereDate('job_assignments.start_at', $now); // Exact match with today's date
                // ->orWhereDate('job_assignments.end_at', $now); // Exact match with today's date
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
        $vehiclesQuery = Vehicle::query()
                                ->where('data_status', 0); // This filter is correctly applied
        $vehicles = $vehiclesQuery->get();
        
        $jobs = JobAssignment::select('job_assignments.id', 'job_assignments.job_type', 'job_assignments.job_record_id')
            ->join('job_assignment_personnels', 'job_assignments.id', '=', 'job_assignment_personnels.job_assignment_id')
            ->where('job_assignment_personnels.user_id', auth()->user()->id)
            ->where('job_assignment_personnels.is_booker', 1)
            ->whereNot('job_assignments.job_status', 3)
            ->where(function ($query) use ($now) {
                $query->where('job_assignments.start_at', '>=', $now) // Job starts today or earlier
                    // ->where('job_assignments.end_at', '>=', $now) // Job ends today or later
                    ->orWhereDate('job_assignments.start_at', $now); // Exact match with today's date
                // ->orWhereDate('job_assignments.end_at', $now); // Exact match with today's date
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

    public function show($id)
    {
        $booking = VehicleBooking::with(['vehicle', 'jobAssignment', 'creator'])->findOrFail($id);

        return view('vehicle_booking.view', compact('booking'))->with('title', 'Create a Vehicle Booking')->with('breadcrumb', ['Home', 'Staff Task', 'Vehicle Booking', 'Detail a Vehicle Booking']);
    }

    public function commonShow($id)
    {
        $booking = VehicleBooking::with(['vehicle', 'jobAssignment', 'creator'])->findOrFail($id);

        return response()->json($booking);
    }

    public function store(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'vehicle_id' => [
                'required',
                Rule::exists('vehicles', 'id'),
                function ($attribute, $value, $fail) use ($request) {
                    $exists = VehicleBooking::where('vehicle_id', $value)
                        ->where(function ($query) use ($request) {
                            $query->whereBetween('start_at', [$request->start_at, $request->end_at])
                                ->orWhereBetween('end_at', [$request->start_at, $request->end_at])
                                ->orWhere(function ($q) use ($request) {
                                    $q->where('start_at', '<=', $request->start_at)
                                        ->where('end_at', '>=', $request->end_at);
                                });
                        })
                        ->exists();

                    if ($exists) {
                        $fail('This vehicle is already booked within the selected dates.');
                    }
                },
            ],
            'start_at' => 'required|date|after_or_equal:today',
            'end_at' => 'required|date|after:start_at',
            'purposes' => 'required|string',
            'job_assignment_id' => 'nullable|exists:job_assignments,id',
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
        if (! $booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        // $booking->update(['end_at' => $now]); // OR $booking->delete();
        $booking->end_at = $now->modify('-1 day');
        $booking->start_at = $now->modify('-1 day');
        $booking->save();

        return response()->json(['message' => 'Vehicle booking canceled successfully']);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $now = Carbon::now(); // Get current date & time
            // Filter bookings where end_at is today or in the future
            $data = VehicleBooking::where('created_by', auth()->user()->id)
                ->where('end_at', '>=', $now)
                ->select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->vehicle->name;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group" aria-label="Basic example"><a href="'.route('v1.vehicle-bookings.edit', ['id' => $row->id]).'" class="edit btn btn-success btn-sm">Edit</a>';
                    $btn .= '<button class="edit btn btn-info btn-sm view-booking"
            id="vehiclebookingid"
            data-id="'.$row->id.'"
            data-bs-toggle="modal"
            data-bs-target="#bookingModal">Detail</button>';
                    $btn .= '<a href="javascript:void(0);"
                class="btn btn-danger btn-sm cancel-booking"
                data-id="'.$row->id.'"
                data-bs-toggle="modal"
                data-bs-target="#cancelConfirmModal">Cancel</a>';

                    return $btn;
                })
                ->rawColumns(['name', 'action'])
                ->make(true);
        }
    }

    public function getHistoryData(Request $request)
    {
        if ($request->ajax()) {
            $now = Carbon::now(); // Get current date & time
            // Filter bookings where end_at is today or in the future
            $data = VehicleBooking::where('created_by', auth()->user()->id)
                ->where('end_at', '<', $now)
                ->orderBy('start_at', 'desc')
                ->select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->vehicle->name;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="edit btn btn-info btn-sm view-booking"
            id="vehiclebookingid"
            data-id="'.$row->id.'"
            data-bs-toggle="modal"
            data-bs-target="#bookingModal">Detail</button>';

                    return $btn;
                })
                ->rawColumns(['name', 'action'])
                ->make(true);
        }
    }

    public function getAvailableVehicles(Request $request)
    {
        $startAt = $request->query('start_at');
        $endAt = $request->query('end_at');
        $id = $request->query('id');

        if (! $startAt || ! $endAt) {
            return response()->json(['error' => 'Start and End date are required'], 400);
        }

        $bookedVehicleIds = VehicleBooking::where(function ($query) use ($startAt, $endAt) {
            $query->whereBetween('start_at', [$startAt, $endAt])
                ->orWhereBetween('end_at', [$startAt, $endAt])
                ->orWhere(function ($query) use ($startAt, $endAt) {
                    $query->where('start_at', '<=', $startAt)
                        ->where('end_at', '>=', $endAt);
                });
        })
            ->pluck('vehicle_id');
        $bookedVehicleId = VehicleBooking::where('id', $id)
            ->pluck('vehicle_id');

        if (! empty($id)) {
            $availableVehiclesAllSelf = Vehicle::where(function ($query) use ($bookedVehicleId, $bookedVehicleIds) {
                $query->where('id', $bookedVehicleId)
                    ->orWhere(function ($subQuery) use ($bookedVehicleIds) {
                        $subQuery->whereNotIn('id', $bookedVehicleIds);
                    });
            });
            if (auth()->check() && !auth()->user()->can('view-vehicle-booking-sedan')) {
                $availableVehiclesAllSelf->where(\DB::raw('UPPER(name)'), 'NOT LIKE', '%' . Str::upper('sedan') . '%');
            }    
            $availableVehiclesAllSelf->where('data_status', 0)
                ->get();

            //dd($availableVehiclesAllSelf->toSql());
            //dd($availableVehiclesAllSelf->getBindings());
       
            // Append image URL (assuming image field exists)
            $availableVehiclesAllSelf->each(function ($vehicle) {
                $vehicle->image_url = asset($vehicle->path_image ?? 'default-image.jpg');
            });

            return response()->json($availableVehiclesAllSelf);
        } else {
            $availableVehiclesAllQuery = Vehicle::whereNotIn('id', $bookedVehicleIds)
                ->where('data_status', 0);
                if (auth()->user()->can('view-vehicle-booking-sedan')==false) {
                    $availableVehiclesAllQuery->where(\DB::raw('UPPER(name)'), 'NOT LIKE', '%' . \Str::upper('sedan') . '%');
                }
                $availableVehiclesAll = $availableVehiclesAllQuery->get();
            $availableVehiclesAll->each(function ($vehicle) {
                $vehicle->image_url = asset($vehicle->path_image ?? 'default-image.jpg');
            });

            return response()->json($availableVehiclesAll);
        }
    }
}
