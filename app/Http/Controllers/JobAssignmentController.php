<?php

namespace App\Http\Controllers;

use App\Models\JobType;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Models\JobAssignment;
use App\Models\JobAssignmentPersonnel;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class JobAssignmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('job_assignment.index', compact('user'))->with('title', 'Job Requisition Form')->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition Form']);
    }
    public function create()
    {
        $user = auth()->user();
        $job_type = JobType::all();
        $job_no = $this->generate_autonumber('JA' . date('ym'));
        $users = User::whereNotIn('id', [auth()->id()])->get()->groupBy('department');
        $vehicles = Vehicle::all();
        return view('job_assignment.form', compact('user', 'job_type', 'job_no', 'users', 'vehicles'))
            ->with('title', 'Job Requisition Form')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition Form', 'Create']);
    }

    public function list(Request $request)
    {
        $user = auth()->user();
        $job_type = JobType::all();
        $job_no = $this->generate_autonumber('JA' . date('ym'));
        $users = User::whereNotIn('id', [auth()->id()])->get()->groupBy('department');
        $vehicles = Vehicle::all();
        return view('job_assignment.list', compact('user', 'job_type', 'job_no', 'users', 'vehicles'))
            ->with('title', 'View Job Requisition')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition Form', 'View Job Requisition']);
    }

    public function respond(Request $request)
    {
        $person = JobAssignmentPersonnel::where('id', $request->input('id'))->first();
        $person->assignment_status = 1;
        $person->save();

        $count = JobAssignmentPersonnel::where('job_assignment_id', $request->input('job_id'))->count();

        /**$status = JobAssignmentPersonnel::where('job_assignment_id', $request->input('job_id'))
        ->where('assignment_status', '!=', 0)
        ->where('assignment_status', '!=', 2)
        ->count();
        if($status>0 && $count ==$status){
            $job = JobAssignment::find($request->input('job_id'));
            $job->job_status=1;
            $job->save();
        } else {
            $status = JobAssignmentPersonnel::where('job_assignment_id', $request->input('job_id'))
            ->where('assignment_status', '!=', 2)
            ->count();
            if($status>0 ){
                $job = JobAssignment::find($request->input('job_id'));
                $job->job_status=2;
                $job->save();
            }


        }**/
        $personnel = JobAssignmentPersonnel::where('job_assignment_id', $request->input('job_id'))->get();

        $totalPersonnel = $personnel->count();
        $acceptedCount = 0;

        foreach ($personnel as $row) {
            if ($row->assignment_status == 2) {
                // If any person rejected, set job assignment to rejected
                JobAssignment::where('id', $request->input('job_id'))->update(['job_status' => 2]);
                //return response()->json(['message' => 'Job Assignment Rejected']);
            }

            if ($row->assignment_status == 1) {
                $acceptedCount++;
            }
        }

        // If all persons accepted, set job assignment to accepted
        if ($acceptedCount == $totalPersonnel && $totalPersonnel > 0) {
            JobAssignment::where('id', $request->input('job_id'))->update(['job_status' => 1]);
            //return response()->json(['message' => 'Job Assignment Accepted']);
        }

        // Otherwise, set job assignment to pending
        JobAssignment::where('id', $request->input('job_id'))->update(['job_status' => 0]);
        //return response()->json(['message' => 'Job Assignment Pending']);



        return redirect()->route('v1.job-assignment-form.view', ['id' => $request->input('job_id'), 'respond' => 'yes'])->with('success', 'Job Requisition Form Created Successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_record_id' => 'required|string',
            'job_type_id' => 'required|exists:job_types,id',
            'business_name' => 'required|string|max:255',
            'business_address' => 'required|string|max:255',
            'scope_of_work' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'prsonnel_ids' => 'array', // Ensure it's an array
            'prsonnel_ids.*' => 'exists:users,id', // Ensure personnel exist in users table
            'vehicle_id' => 'nullable|exists:vehicles,id'
        ]);

        // Create Job Assignment Form
        $jobAssignment = JobAssignment::create([
            'job_record_id' => $validated['job_record_id'],
            'job_type_id' => $validated['job_type_id'],
            'business_name' => $validated['business_name'],
            'business_address' => $validated['business_address'],
            'scope_of_work' => $validated['scope_of_work'],
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'vehicle_id' => $request->vehicle_id ?? null,
            'user_id' => Auth()->user()->id
        ]);

        // Attach personnel to job assignment (Many-to-Many Relationship)
        if (!empty($validated['prsonnel_ids'])) {
            $jobAssignment->personnel()->attach($validated['prsonnel_ids']);
        }

        // Redirect back with success message
        return redirect()->route('v1.job-assignment-form')->with('success', 'Job Requisition Form Created Successfully');
    }

    public function view($id, $respond)
    {
        $user = auth()->user();
        $job = JobAssignment::find($id);
        $personnels = JobAssignmentPersonnel::where('job_assignment_id', $id)->get();
        return view('job_assignment.view', compact('user', 'job', 'personnels', 'respond'))
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
            $data = JobAssignment::select(
                'job_assignments.id',
                'job_assignments.job_record_id',
                DB::raw("DATE_FORMAT(job_assignments.created_at, '%Y-%m-%d %H:%i') as created_date"),
                'job_assignments.business_name',
                'job_assignments.start_at',
                'job_assignments.end_at',
                'job_assignments.job_status',
                'job_types.name as job_type_name' // Select job type name
            )
                ->join('job_types', 'job_assignments.job_type_id', '=', 'job_types.id')
                ->where('job_assignments.user_id', auth()->user()->id);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->job_status == 0) {
                        $status = "<span class='badge bg-info'>Pending</span>";
                    } elseif ($row->job_status == 1) {
                        $status = "<span class='badge bg-success'>Accepted</span>";
                    } else {
                        $status = "<span class='badge bg-danger'>Rejected</span>";
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('v1.job-assignment-form.view', ['id' => $row->id, 'respond' => 'no']) . '" class="edit btn btn-primary btn-sm">View</a>';
                    return $btn;
                })
                ->addColumn('date_range', function ($row) {
                    return $row->start_at . ' - ' . $row->end_at;
                })
                ->rawColumns(['date_range'])
                ->rawColumns(['status', 'date_range', 'action'])
                ->make(true);
        }
    }

    public function getJobsAssignmentsByUser(Request $request)
    {
        if ($request->ajax()) {
            $data = JobAssignment::select(
                'job_assignments.id',
                'job_assignments.job_record_id',
                DB::raw("DATE_FORMAT(job_assignments.created_at, '%Y-%m-%d %H:%i') as created_date"),
                'job_assignments.business_name',
                'job_assignments.start_at',
                'job_assignments.end_at',
                'job_assignments.job_status',
                'job_types.name as job_type_name' // Select job type name
            )
                ->join('job_types', 'job_assignments.job_type_id', '=', 'job_types.id')
                ->join('job_assignment_personnels', 'job_assignments.id', '=', 'job_assignment_personnels.job_assignment_id')
                ->where('job_assignment_personnels.user_id', auth()->user()->id);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->job_status == 0) {
                        $status = "<span class='badge bg-info'>Pending</span>";
                    } elseif ($row->job_status == 1) {
                        $status = "<span class='badge bg-success'>Accepted</span>";
                    } else {
                        $status = "<span class='badge bg-danger'>Rejected</span>";
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    if ($row->job_status == 0) {
                        $label = "Respond";
                    } else {
                        $label = "View";
                    }
                    $btn = '<a href="' . route('v1.job-assignment-form.view', ['id' => $row->id, 'respond' => 'yes']) . '" class="edit btn btn-primary btn-sm">' . $label . '</a>';
                    return $btn;
                })
                ->addColumn('date_range', function ($row) {
                    return $row->start_at . ' - ' . $row->end_at;
                })
                ->rawColumns(['date_range'])
                ->rawColumns(['status', 'date_range', 'action'])
                ->make(true);
        }
    }

}
