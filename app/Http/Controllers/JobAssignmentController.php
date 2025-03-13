<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\JobType;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Models\JobAssignment;
use App\Models\JobAssignmentPersonnel;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Mail\ConfirmationMail;
use Illuminate\Support\Facades\Mail;

class JobAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-job-requisition', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-job-requisition', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-job-requisition', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-job-requisition', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('job_assignment.index')->with('title', 'Job Requisition Form')->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition Form']);
    }
    public function create()
    {
        $job_no = $this->generate_autonumber('JA' . date('ym'));
        $users1 = User::whereNot('position_level_id', 2)
            //->whereNotIn('users.id', [auth()->id()])
            ->join('departments', 'departments.id', '=', 'users.department_id') // Join 1st dept
            ->select('users.*', 'departments.name as department_name'); // Select necessary fields

        $users2 = User::whereNot('position_level_id', 2)
            //->whereNotIn('users.id', [auth()->id()])
            ->join('departments', 'departments.id', '=', 'users.2nd_department_id') // Join 1st dept
            ->select('users.*', 'departments.name as department_name'); // Select necessary fields




        $users = $users1->union($users2)->get()->groupBy('department_name');

        $vehicles = Vehicle::all();
        return view('job_assignment.form', compact('job_no', 'users', 'vehicles'))
            ->with('title', 'Job Requisition Form')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition Form', 'Create']);
    }

    public function edit($id)
    {
        $job = JobAssignment::findOrFail($id);

        $users1 = User::whereNot('position_level_id', 2)
            ->join('departments', 'departments.id', '=', 'users.department_id')
            ->select('users.*', 'departments.name as department_name');

        $users2 = User::whereNot('position_level_id', 2)
            ->join('departments', 'departments.id', '=', 'users.2nd_department_id')
            ->select('users.*', 'departments.name as department_name');

        $users = $users1->union($users2)->get()->groupBy('department_name');

        $vehicles = Vehicle::all();
        $selectedUsers = $job->personnel()->pluck('users.id')->toArray(); // Fetch selected users

        return view('job_assignment.edit', compact('job', 'users', 'vehicles', 'selectedUsers'))
            ->with('title', 'Edit Job Requisition Form')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition Form', 'Edit']);
    }

    public function list(Request $request)
    {
        $job_type = JobType::all();
        $job_no = $this->generate_autonumber('JA' . date('ym'));
        $users = User::whereNotIn('id', [auth()->id()])->get()->groupBy('department');
        $vehicles = Vehicle::all();
        return view('job_assignment.list', compact('job_type', 'job_no', 'users', 'vehicles'))
            ->with('title', 'View Job Requisition')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition Form', 'View Job Requisition']);
    }

    public function respond(Request $request)
    {
        $response = $request->input('response');
        $person = JobAssignmentPersonnel::where('id', $request->input('id'))->first();
        $jobAssignment = JobAssignment::find($person->job_assignment_id);
        $creator = User::find($jobAssignment->user_id);
        $member = User::find($person->user_id);
        if ($response === 'accept') {
            $person->assignment_status = 1;

            $creator->notify(new UserNotification(
                $member->name . ' <strong>accepted</strong> invitation at Job ID ' . $jobAssignment->job_record_id,
                'accept',
                route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
            ));
        } else if ($response === 'decline') {
            $person->assignment_status = 2;
            $person->reason = $request->input('reason');
            $person->purpose_at = $request->input('purpose_at');

            $creator->notify(new UserNotification(
                $member->name . ' <strong>declined</strong> invitation at Job ID ' . $jobAssignment->job_record_id,
                'decline',
                route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
            ));
        } else if ($response === 'confirm') {
            $person->assignment_status = 1;

            $creator->notify(new UserNotification(
                auth()->user()->name . ' <strong>confirmed</strong> invitation at Job ID ' . $jobAssignment->job_record_id,
                'accept',
                route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
            ));
        }
        $person->save();

        $count = JobAssignmentPersonnel::where('job_assignment_id', $request->input('job_id'))->count();

        $personnel = JobAssignmentPersonnel::where('job_assignment_id', $request->input('job_id'))->get();




        return redirect()->route('v1.job-assignment-form.view', ['id' => $request->input('job_id'), 'respond' => 'yes'])->with('success', 'Job Requisition Form has been respond Successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_record_id' => 'required|string',
            'job_type' => 'required',
            'business_name' => 'required|string|max:255',
            'business_address' => 'required|string|max:255',
            'scope_of_work' => 'required|string',
            'start_at' => 'required|date|after_or_equal:today', // ðŸš€ Prevent past dates
            'end_at' => 'required|date|after_or_equal:start_at', // âœ… Must be after or same as start_at
            'prsonnel_ids' => 'array',
            'prsonnel_ids.*' => 'exists:users,id',
            'job_status' => 'nullable'
        ]);


        // Create Job Assignment Form
        $jobAssignment = JobAssignment::create([
            'job_record_id' => $validated['job_record_id'],
            'job_type' => $validated['job_type'],
            'business_name' => $validated['business_name'],
            'business_address' => $validated['business_address'],
            'scope_of_work' => $validated['scope_of_work'],
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'is_vehicle_require' => $validated['is_vehicle_require'] ?? 0,
            'user_id' => Auth()->user()->id,
            'job_status' => $request->has('job_status') ? 1 : 0,
        ]);

        // Attach personnel to job assignment (Many-to-Many Relationship)
        if (!empty($validated['prsonnel_ids'])) {
            $jobAssignment->personnel()->attach($validated['prsonnel_ids']);
            foreach ($validated['prsonnel_ids'] as $personnelId) {
                $user = User::find($personnelId);
                $user->notify(new UserNotification(
                    'You has been invited at Job ID ' . $jobAssignment->job_record_id,
                    'success',
                    route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
                ));
            }
        }

        // Redirect back with success message
        return redirect()->route('v1.job-assignment-form.list')->with('success', 'Job Requisition Form Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'job_record_id' => 'required|string',
            'job_type' => 'required',
            'business_name' => 'required|string|max:255',
            'business_address' => 'required|string|max:255',
            'scope_of_work' => 'required|string',
            'start_at' => 'required|date|after_or_equal:today', // Prevent past dates
            'end_at' => 'required|date|after_or_equal:start_at', // Must be after or same as start_at
            'prsonnel_ids' => 'array',
            'prsonnel_ids.*' => 'exists:users,id',
            'job_status' => 'nullable'
        ]);

        // Find the existing job assignment
        $jobAssignment = JobAssignment::findOrFail($id);

        // Update job assignment data
        $jobAssignment->update([
            'job_record_id' => $validated['job_record_id'],
            'job_type' => $validated['job_type'],
            'business_name' => $validated['business_name'],
            'business_address' => $validated['business_address'],
            'scope_of_work' => $validated['scope_of_work'],
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'is_vehicle_require' => $validated['is_vehicle_require'] ?? 0,
            'user_id' => auth()->user()->id,
            'job_status' => $request->has('job_status') ? 1 : 0,
        ]);

        // Sync personnel (Many-to-Many Relationship)
        if (!empty($validated['prsonnel_ids'])) {
            $jobAssignment->personnel()->sync($validated['prsonnel_ids']); // Sync instead of attach

            foreach ($validated['prsonnel_ids'] as $personnelId) {
                $user = User::find($personnelId);
                $user->notify(new UserNotification(
                    'Job ID ' . $jobAssignment->job_record_id . ' has been updated. Please check the details.',
                    'info',
                    route('v1.job-assignment-form.view', ['id' => $id, 'respond' => 'yes'])
                ));
            }
        } else {
            $jobAssignment->personnel()->detach(); // Remove all personnel if none are selected
        }

        // Redirect back with success message
        return redirect()->route('v1.job-assignment-form.view', ['id' => $id, 'respond' => 'yes'])->with('success', 'Job Requisition Form has been respond Successfully');
    }


    public function view($id, $respond)
    {
        $notif = request('notif');

        if ($id) {
            Auth::user()->notifications()
                ->where('id', $notif)
                ->update(['read_at' => now()]);
        }

        $job = JobAssignment::find($id);
        $staff = User::whereNot('id', auth()->user()->id)
            ->whereNot('position_level_id', 9)
            ->whereDoesntHave('jobAssignmentPersonnel', function ($query) use ($id) {
                $query->where('job_assignment_id', $id);
            })
            ->get();
        $personnels = JobAssignmentPersonnel::where('job_assignment_id', $id)->get();
        return view('job_assignment.view', compact('job', 'personnels', 'respond', 'staff'))
            ->with('title', 'Job Requisition Form Detail')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition Form', 'Detail']);
    }

    function generate_autonumber($prefix = 'XXX')
    {
        // Get the last inserted autonumber (if any)
        $last_autonumber = DB::table('job_assignments')
            ->whereYear('created_at', date('Y')) // Filter by year
            ->whereMonth('created_at', date('m')) // Filter by month
            ->latest('id')
            ->value('job_record_id'); // Replace with the actual column name

        if (!$last_autonumber) {
            // If no previous autonumber exists, start with the initial value
            $initial_number = 1;
        } else {
            // Extract the numeric part of the last autonumber
            $last_number = (int) substr($last_autonumber, strlen($prefix));
            $initial_number = $last_number + 1;
        }

        // Format the autonumber with leading zeros
        $padded_number = str_pad($initial_number, 5, '0', STR_PAD_LEFT);

        // Concatenate prefix and padded number
        $autonumber = $prefix . $padded_number;

        return $autonumber;
    }

    public function getJobsAssignments(Request $request)
    {
        if ($request->ajax()) {
            $now = Carbon::now(); // Get current date-time

            $data = JobAssignment::select(
                'job_assignments.id',
                'job_assignments.job_record_id',
                DB::raw("DATE_FORMAT(job_assignments.created_at, '%Y-%m-%d %H:%i') as created_date"),
                'job_assignments.business_name',
                'job_assignments.start_at',
                'job_assignments.end_at',
                'job_assignments.job_status',
                'job_assignments.job_type as job_type_name'
            )
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

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->job_status == 0) {
                        return "<span class='badge bg-info'>Pending</span>";
                    } elseif ($row->job_status == 1) {
                        return "<span class='badge bg-success'>Accepted</span>";
                    } else {
                        return "<span class='badge bg-danger'>Rejected</span>";
                    }
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('v1.job-assignment-form.view', ['id' => $row->id, 'respond' => 'no']) . '" class="edit btn btn-primary btn-sm">View</a>';
                })
                ->addColumn('date_range', function ($row) {
                    return $row->start_at . ' - ' . $row->end_at;
                })
                ->rawColumns(['status', 'date_range', 'action'])
                ->make(true);
        }
    }

    public function getJobsAssignmentHistories(Request $request)
    {
        if ($request->ajax()) {
            $now = Carbon::now(); // Get current date-time

            $data1 = JobAssignment::select(
                'job_assignments.id',
                'job_assignments.job_record_id',
                DB::raw("DATE_FORMAT(job_assignments.created_at, '%Y-%m-%d %H:%i') as created_date"),
                'job_assignments.business_name',
                'job_assignments.start_at',
                'job_assignments.end_at',
                'job_assignments.job_status',
                'job_assignments.job_type as job_type_name',
                'departments.id as department_id',
                'departments.name as department_name',
                'users.name as user_name',
                'job_assignments.user_id'
            )
                ->join('users', 'job_assignments.user_id', '=', 'users.id')
                ->join('departments', 'users.department_id', '=', 'departments.id')
                ->where('job_assignments.user_id', auth()->user()->id)
                ->whereNotNull('job_assignments.end_at')
                ->where('job_assignments.end_at', '<', $now); // Past jobs condition

            // Query for ongoing or future jobs
            $data2 = JobAssignment::select(
                'job_assignments.id',
                'job_assignments.job_record_id',
                DB::raw("DATE_FORMAT(job_assignments.created_at, '%Y-%m-%d %H:%i') as created_date"),
                'job_assignments.business_name',
                'job_assignments.start_at',
                'job_assignments.end_at',
                'job_assignments.job_status',
                'job_assignments.job_type as job_type_name',
                'departments.id as department_id',
                'departments.name as department_name',
                'users.name as user_name',
                'job_assignments.user_id'
            )
                ->join('job_assignment_personnels', 'job_assignments.id', '=', 'job_assignment_personnels.job_assignment_id')
                ->join('users as user_assigned', 'job_assignment_personnels.user_id', '=', 'user_assigned.id')
                ->join('users', 'job_assignments.user_id', '=', 'users.id')
                ->join('departments', 'users.department_id', '=', 'departments.id')
                ->where('job_assignment_personnels.user_id', auth()->user()->id)
                ->where('job_assignments.end_at', '<', $now); // Jobs with no end date



            $data3 = JobAssignment::select(
                'job_assignments.id',
                'job_assignments.job_record_id',
                DB::raw("DATE_FORMAT(job_assignments.created_at, '%Y-%m-%d %H:%i') as created_date"),
                'job_assignments.business_name',
                'job_assignments.start_at',
                'job_assignments.end_at',
                'job_assignments.job_status',
                'job_assignments.job_type as job_type_name',
                'departments.id as department_id',
                'departments.name as department_name',
                'users.name as user_name',
                'job_assignments.user_id'
            )
                ->join('users', 'job_assignments.user_id', '=', 'users.id')
                ->join('departments', 'users.department_id', '=', 'departments.id')
                ->where('job_assignments.user_id', auth()->user()->id)
                ->where('job_assignments.job_status', 3); // ✅ Fetch only status 3



            // Combine both queries with UNION
            $data = $data1
                ->union($data2)
                ->union($data3)
                ->orderBy('created_date', 'DESC')
                ->get();



            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->job_status == 0) {
                        return "<span class='badge bg-info'>Pending</span>";
                    } elseif ($row->job_status == 1) {
                        return "<span class='badge bg-success'>Accepted</span>";
                    } elseif ($row->job_status == 2) {
                        return "<span class='badge bg-danger'>Rejected</span>";
                    } else {
                        return "<span class='badge bg-danger'>Canceled</span>";
                    }
                })
                ->addColumn('department', function ($row) {
                    return $row->department_name;
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('v1.job-assignment-form.view', ['id' => $row->id, 'respond' => 'no']) . '" class="edit btn btn-primary btn-sm">View</a>';
                })
                ->addColumn('date_range', function ($row) {
                    return $row->start_at . ' - ' . $row->end_at;
                })
                ->rawColumns(['status', 'date_range', 'action', 'department'])
                ->make(true);
        }
    }


    public function getJobsAssignmentsByUser(Request $request)
    {
        if ($request->ajax()) {
            $now = Carbon::now(); // Get current date-time

            $data = JobAssignment::select(
                'job_assignments.id',
                'job_assignments.job_record_id',
                DB::raw("DATE_FORMAT(job_assignments.created_at, '%Y-%m-%d %H:%i') as created_date"),
                'job_assignments.business_name',
                'job_assignments.start_at',
                'job_assignments.end_at',
                'job_assignments.job_status',
                'job_assignments.job_type as job_type_name'
            )
                ->join('job_assignment_personnels', 'job_assignments.id', '=', 'job_assignment_personnels.job_assignment_id')
                ->where('job_assignment_personnels.user_id', auth()->user()->id)
                ->whereNot('job_assignments.job_status', 3)
                ->where(function ($query) use ($now) {
                    $query->where('job_assignments.start_at', '>=', $now) // Job starts today or earlier
                        //->where('job_assignments.end_at', '>=', $now) // Job ends today or later
                        ->orWhereDate('job_assignments.start_at', $now); // Exact match with today's date
                    //->orWhereDate('job_assignments.end_at', $now); // Exact match with today's date
                })
                ->orderBy('job_assignments.created_at', 'DESC');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->job_status == 0) {
                        return "<span class='badge bg-info'>Pending</span>";
                    } elseif ($row->job_status == 1) {
                        return "<span class='badge bg-success'>Accepted</span>";
                    } else {
                        return "<span class='badge bg-danger'>Rejected</span>";
                    }
                })
                ->addColumn('action', function ($row) {
                    $label = ($row->job_status == 0) ? "Respond" : "View";
                    return '<a href="' . route('v1.job-assignment-form.view', ['id' => $row->id, 'respond' => 'yes']) . '" class="edit btn btn-primary btn-sm">' . $label . '</a>';
                })
                ->addColumn('date_range', function ($row) {
                    return $row->start_at . ' - ' . $row->end_at;
                })
                ->rawColumns(['status', 'date_range', 'action'])
                ->make(true);
        }
    }


    public function invitedStaff($user_id, $job_id)
    {
        $jobAssignment = JobAssignment::findOrFail($job_id);
        $data = [
            'user_id' => $user_id,
            'job_assignment_id' => $jobAssignment->id,
            'assignment_status' => 3
        ];
        JobAssignmentPersonnel::create($data);

        $creator = User::find($jobAssignment->user_id);
        $member = User::find($user_id);
        $member->notify(new UserNotification(
            auth()->user()->name . ' <strong>Invited</strong> you at Job ID ' . $jobAssignment->job_record_id,
            'success',
            route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
        ));
        $creator->notify(new UserNotification(
            auth()->user()->name . ' <strong>Invited</strong> ' . $member->name . ' at Job ID ' . $jobAssignment->job_record_id,
            'success',
            route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
        ));
        return redirect()->route('v1.job-assignment-form.view', ['id' => $job_id, 'respond' => 'yes'])->with('success', 'Personnel has been involved Successfully');
    }

    public function history()
    {
        $job_type = JobType::all();
        $job_no = $this->generate_autonumber('JA' . date('ym'));
        $users = User::whereNotIn('id', [auth()->id()])->get()->groupBy('department');
        $vehicles = Vehicle::all();
        $departments = Department::all();
        $persons = User::all();
        return view('job_assignment.history', compact('persons', 'departments', 'job_type', 'job_no', 'users', 'vehicles'))
            ->with('title', 'Job Requisition History')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition Form', 'Job Requisition History']);
    }

    public function updateJobAssignmentStatus(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'action' => 'required|in:publish,recall',
        ]);

        $event = JobAssignment::find($request->id); // Example: Find an event (replace with your logic)

        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event not found.']);
        }

        // Update status based on action
        if ($request->action === "publish") {
            $event->is_publish = 1; // Published
        } elseif ($request->action === "recall") {
            $event->is_publish = 0; // Canceled
        }

        $event->save();
        $personnels = JobAssignmentPersonnel::where('job_assignment_id', $event->id)->get();
        $personnelIds = $personnels->pluck('user_id')->toArray();

        if (!empty($personnelIds)) {   // ✅ Check if personnel exist

            $event->personnel()->attach($personnelIds);
            foreach ($personnels as $personnelId) {
                $user = User::find($personnelId->user_id);
                if ($user) {  // ✅ Ensure the user exists before sending notification
                    $user->notify(new UserNotification(
                        auth()->user()->name . ' was ' . $request->action . 'ed to Job ID ' . $event->job_record_id,
                        'success',
                        route('v1.job-assignment-form.view', ['id' => $event->id, 'respond' => 'yes'])
                    ));
                }
            }
        }
        if ($request->action === "cancel") {
            return response()->json(['success' => true, 'message' => 'Job Requisition Form has been canceled!']);
        } else {
            return response()->json(['success' => true, 'message' => 'Job Requisition Form has been published!']);
        }

    }

    public function updateJobAssignmentVehicleRequire(Request $request)
    {
        $job = JobAssignment::findOrFail($request->id);
        $job->is_vehicle_require = $request->is_vehicle_require;
        $job->save();

        return response()->json(['success' => true, 'message' => 'Vehicle Status updated successfully']);
    }


    public function sendBookingEmail()
    {
        $booking = [
            'name' => 'Teguh Susanto',
            'start_at' => '2025-03-15',
            'end_at' => '2025-03-20',
            'purposes' => 'Business Trip'
        ];

        Mail::to('teguh.susanto@hotmail.com')->send(new ConfirmationMail($booking));

        return response()->json(['message' => 'Booking confirmation email sent!']);
    }

}
