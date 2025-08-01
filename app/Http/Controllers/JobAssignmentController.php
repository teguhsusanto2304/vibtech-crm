<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmationMail;
use App\Models\Department;
use App\Models\JobAssignment;
use App\Models\JobAssignmentFile;
use App\Models\JobAssignmentPersonnel;
use App\Models\JobType;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\EmailBatch;
use App\Notifications\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendBulkEmailJob;

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
        return view('job_assignment.index')->with('title', 'Job Requisition')->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition']);
    }

    public function create()
    {
        $job_no = $this->generate_autonumber('JA'.date('ym'));
        $users1 = User::whereNot('position_level_id', 2)
            ->where('user_status', 1)
            ->join('departments', 'departments.id', '=', 'users.department_id') // Join 1st dept
            ->select('users.*', 'departments.name as department_name'); // Select necessary fields

        $users2 = User::whereNot('position_level_id', 2)
            ->where('user_status', 1)
            ->join('departments', 'departments.id', '=', 'users.2nd_department_id') // Join 1st dept
            ->select('users.*', 'departments.name as department_name'); // Select necessary fields

        $authUser = auth()->user(); // Get logged-in user
        $authDepartment = $authUser->department_id;
        $authDepartment2nd = $authUser['2nd_department_id'];
        if (!is_null($authDepartment2nd)) {
            $users3 = User::where('department_id', $authDepartment)
            ->orWhere('2nd_department_id', $authDepartment)
            ->where('position_level_id', 2)
            ->where('user_status', 1)
            ->join('departments', 'departments.id', '=', 'users.department_id')
            ->select('users.*', 'departments.name as department_name');

            $users4 = User::where(['department_id'=> $authDepartment2nd,'user_status'=>1])
                ->orWhere(['2nd_department_id'=> $authDepartment2nd,'user_status'=>1])
                ->where('position_level_id', 2)
                ->where('user_status', 1)
                ->join('departments', 'departments.id', '=', 'users.department_id')
                ->select('users.*', 'departments.name as department_name');

            // Union all queries without post-union where
            $users = $users1->union($users2)->union($users3)->union($users4)->get()->groupBy('department_name');
        } else {
            $users = $users1->union($users2)->get()->groupBy('department_name');
        }

        
        $vehicles = Vehicle::all();

        return view('job_assignment.form', compact('job_no', 'users', 'vehicles'))
            ->with('title', 'Job Requisition')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition', 'Create']);
    }

    public function edit($id)
    {
        $job = JobAssignment::findOrFail($id);

        $users1 = User::whereNot('position_level_id', 2)
            ->where('user_status', 1)
            ->join('departments', 'departments.id', '=', 'users.department_id') // Join 1st dept
            ->select('users.*', 'departments.name as department_name'); // Select necessary fields

        $users2 = User::whereNot('position_level_id', 2)
            ->where('user_status', 1)
            ->join('departments', 'departments.id', '=', 'users.2nd_department_id') // Join 1st dept
            ->select('users.*', 'departments.name as department_name'); // Select necessary fields

        $authUser = auth()->user(); // Get logged-in user
        $authDepartment = $authUser->department_id;
        $authDepartment2nd = $authUser['2nd_department_id'];
        if (!is_null($authDepartment2nd)) {
            $users3 = User::where('department_id', $authDepartment)
            ->orWhere('2nd_department_id', $authDepartment)
            ->where('position_level_id', 2)
            ->where('user_status', 1)
            ->join('departments', 'departments.id', '=', 'users.department_id')
            ->select('users.*', 'departments.name as department_name');

            $users4 = User::where(['department_id'=> $authDepartment2nd,'user_status'=>1])
                ->orWhere(['2nd_department_id'=> $authDepartment2nd,'user_status'=>1])
                ->where('position_level_id', 2)
                ->where('user_status', 1)
                ->join('departments', 'departments.id', '=', 'users.department_id')
                ->select('users.*', 'departments.name as department_name');

            // Union all queries without post-union where
            $users = $users1->union($users2)->union($users3)->union($users4)->get()->groupBy('department_name');
        } else {
            $users = $users1->union($users2)->get()->groupBy('department_name');
        }

        $vehicles = Vehicle::all();
        $selectedUsers = $job->personnel()->pluck('users.id')->toArray(); // Fetch selected users

        return view('job_assignment.edit', compact('job', 'users', 'vehicles', 'selectedUsers'))
            ->with('title', 'Edit Job Requisition')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition', 'Edit']);
    }

    public function list(Request $request)
    {
        $job_type = JobType::all();
        $job_no = $this->generate_autonumber('JA'.date('ym'));
        $users = User::whereNotIn('id', [auth()->id()])->get()->groupBy('department');
        $vehicles = Vehicle::all();

        return view('job_assignment.list', compact('job_type', 'job_no', 'users', 'vehicles'))
            ->with('title', 'View Job Requisition')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition', 'View Job Requisition']);
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
                $member->name.' <strong>accepted</strong> invitation at Job ID '.$jobAssignment->job_record_id,
                'accept',
                route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
            ));
        } elseif ($response === 'decline') {
            $person->assignment_status = 2;
            $person->reason = $request->input('reason');
            $person->purpose_at = $request->input('purpose_at');

            $creator->notify(new UserNotification(
                $member->name.' <strong>declined</strong> invitation at Job ID '.$jobAssignment->job_record_id,
                'decline',
                route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
            ));
        } elseif ($response === 'confirm') {
            $person->assignment_status = 1;

            $creator->notify(new UserNotification(
                auth()->user()->name.' <strong>confirmed</strong> invitation at Job ID '.$jobAssignment->job_record_id,
                'accept',
                route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
            ));
        } elseif ($response === 'reminder') {
            $jobAssignment = JobAssignment::find($request->input('job_id'));
            $personInvolveds = JobAssignmentPersonnel::where('job_assignment_id', $request->input('job_id'))
                ->whereNot('user_id', auth()->user()->id)
                ->get();
            foreach ($personInvolveds as $personInvolved) {
                $user1 = User::where('id', $personInvolved->user_id)->first();
                try {
                    $user1->notify(new UserNotification(
                        auth()->user()->name.' <strong>Remindered</strong> at Job ID '.$jobAssignment->job_record_id,
                        'accept',
                        route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
                    ));
                } catch (\Exception $e) {
                    \Log::error('Notification failed: '.$e->getMessage());
                }
            }

        }
        $person->save();

        $count = JobAssignmentPersonnel::where('job_assignment_id', $request->input('job_id'))->count();
        $personnel_accepted = JobAssignmentPersonnel::where(['job_assignment_id' => $request->input('job_id'), 'assignment_status' => 1])->count();
        if ($count === $personnel_accepted) {
            $jobAssignment->job_status = 1;
            $jobAssignment->is_publish = 1;
            $jobAssignment->save();
            $creator->notify(new UserNotification(
                'All personnel involved were <strong>Accepted</strong> at Job ID '.$jobAssignment->job_record_id,
                'accept',
                route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
            ));
            $personnels = JobAssignmentPersonnel::where('job_assignment_id', $request->input('job_id'))->get();
            foreach ($personnels as $person) {
                $person->user->notify(new UserNotification(
                    'All personnel involved were <strong>Accepted</strong> at Job ID '.$jobAssignment->job_record_id,
                    'accept',
                    route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
                ));
            }
        }

        return redirect()->route('v1.job-assignment-form.view', ['id' => $request->input('job_id'), 'respond' => 'yes'])->with('success', 'Job Requisition has been respond Successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_record_id' => 'required|string',
            'job_type' => 'required',
            'business_name' => 'required|string|max:255',
            'business_address' => 'required|string|max:255',
            'scope_of_work' => 'required|string',
            'start_at' => 'required|date|after_or_equal:today',
            'end_at' => 'required|date|after_or_equal:start_at',
            'prsonnel_ids' => 'array',
            'prsonnel_ids.*' => 'exists:users,id',
            'job_status' => 'nullable',
            'project_files' => 'nullable|array|max:5',
            'project_files.*' => 'file|mimes:png,jpg,pdf,doc,docx|max:10240',
        ]);

        DB::beginTransaction();

        try {
            // Create Job Assignment
            $jobAssignment = JobAssignment::create([
                'job_record_id' => $validated['job_record_id'],
                'job_type' => $validated['job_type'],
                'business_name' => $validated['business_name'],
                'business_address' => $validated['business_address'],
                'scope_of_work' => $validated['scope_of_work'],
                'start_at' => $validated['start_at'],
                'end_at' => $validated['end_at'],
                'is_vehicle_require' => $request->boolean('is_vehicle_require'),
                'user_id' => auth()->id(),
                'job_status' => $request->filled('job_status'),
            ]);

            // ✅ File uploads
            if ($request->hasFile('project_files')) {
                $descriptions = $request->input('project_file_descriptions', []);
                $filesData = [];

                foreach ($request->file('project_files') as $index => $file) {
                    $filesData[] = [
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $file->store("projects/{$jobAssignment->id}/files", 'public'),
                        'mime_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'description' => $descriptions[$index] ?? null,
                        'uploaded_by_user_id' => auth()->id(),
                    ];
                }

                $jobAssignment->files()->createMany($filesData);
            }

            // ✅ Attach personnel & send notifications in batch
            $personnelIds = $validated['prsonnel_ids'] ?? [];

            if (!empty($personnelIds)) {
                $jobAssignment->personnel()->attach($personnelIds);

                $users = User::whereIn('id', $personnelIds)->get();
                $jobUrl = route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes']);
                $delay = 0;
                $i=1;
                foreach ($users as $user) {
                    // Queue the notification
                    $user->notify(new UserNotification(
                        "You have been invited to Job ID {$jobAssignment->job_record_id}",
                        'success',
                        $jobUrl
                    ));                   


                    // Increase delay every 6 emails to throttle
                    if (($i + 1) % 6 === 0) {
                        $delay += 15; // delay next batch by 5 seconds
                    }


                    // Queue the email
                    $this->sendEmail([
                        'email' => $user->email,
                        'originator' => auth()->user()->name,
                        'personel' => $user->name,
                        'job_record_id' => $validated['job_record_id'],
                        'type_job' => $validated['job_type'],
                        'scope_of_work' => $validated['scope_of_work'],
                        'start_at' => $validated['start_at'],
                        'end_at' => $validated['end_at'],
                        'is_vehicle_require' => $request->has('is_vehicle_require') ? 'Yes' : 'No',
                        'url' => $jobUrl,
                        'delay'=>$delay
                    ]);
                    $i++;
                }
            }

            DB::commit();

            return redirect()->route('v1.job-assignment-form.view', [
                'id' => $jobAssignment->id,
                'respond' => 'no',
            ])->with('success', 'Job Requisition Form Created Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Failed to create Job Assignment: ' . $e->getMessage());
        }
    }


    public function storeOld(Request $request)
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
            'job_status' => 'nullable',
            'project_files' => 'nullable|array|max:5', // Max 5 new files
            'project_files.*' => 'file|mimes:png,jpg,pdf,doc,docx|max:10240',
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
            'is_vehicle_require' => $request->is_vehicle_require ? 1: 0,
            'user_id' => Auth()->user()->id,
            'job_status' => $request->has('job_status') ? 1 : 0,
        ]);

        // 4. Handle File Uploads
            if ($request->hasFile('project_files')) {
                foreach ($request->file('project_files') as $index => $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $fileMimeType = $file->getClientMimeType();
                    $fileSize = $file->getSize(); // Size in bytes

                    // Store file in storage/app/public/projects/{project_id}/files
                    $path = $file->store('projects/' . $jobAssignment->id . '/files', 'public');

                    // Save file details to project_files table
                    $jobAssignment->files()->create([
                        'file_name' => $originalFileName,
                        'file_path' => $path, // The path returned by store()
                        'mime_type' => $fileMimeType,
                        'file_size' => $fileSize,
                        'description' => $request->input('project_file_descriptions')[$index] ?? null,
                        'uploaded_by_user_id' => Auth::id(),
                    ]);
                }
            }

        // Attach personnel to job assignment (Many-to-Many Relationship)
        if (! empty($validated['prsonnel_ids'])) {
            $jobAssignment->personnel()->attach($validated['prsonnel_ids']);
            foreach ($validated['prsonnel_ids'] as $personnelId) {
                $user = User::find($personnelId);
                $user->notify(new UserNotification(
                    'You has been invited at Job ID '.$jobAssignment->job_record_id,
                    'success',
                    route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
                ));
                $data = [
                    'email' => $user->email,
                    'originator' => Auth()->user()->name,
                    'personel' => $user->name,
                    'job_record_id' => $validated['job_record_id'],
                    'type_job' => $validated['job_type'],
                    'scope_of_work' => $validated['scope_of_work'],
                    'start_at' => $validated['start_at'],
                    'end_at' => $validated['end_at'],
                    'is_vehicle_require' => $request->has('is_vehicle_require') ? 'Yes' : 'No',
                    'url' => route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes']),
                ];
                $this->sendEmail($data);
            }
        }

        // Redirect back with success message
        return redirect()->route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'no'])->with('success', 'Job Requisition Form Created Successfully');
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
            'job_status' => 'nullable',
            'project_files' => 'nullable|array|max:5', // Max 5 new files
            'project_files.*' => 'file|mimes:png,jpg,pdf,doc,docx|max:10240',

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
            'job_status' => 0,
        ]);

        // Sync personnel (Many-to-Many Relationship)
        if (! empty($validated['prsonnel_ids'])) {
            $jobAssignment->personnel()->sync($validated['prsonnel_ids']); // Sync instead of attach

            foreach ($validated['prsonnel_ids'] as $personnelId) {
                $user = User::find($personnelId);
                $user->notify(new UserNotification(
                    'Job ID '.$jobAssignment->job_record_id.' has been updated. Please check the details.',
                    'info',
                    route('v1.job-assignment-form.view', ['id' => $id, 'respond' => 'yes'])
                ));
            }
        } else {
            $jobAssignment->personnel()->detach(); // Remove all personnel if none are selected
        }

        if ($request->hasFile('project_files')) {
                foreach ($request->file('project_files') as $index => $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $fileMimeType = $file->getClientMimeType();
                    $fileSize = $file->getSize(); // Size in bytes

                    // Store file in storage/app/public/projects/{project_id}/files
                    $path = $file->store('projects/' . $jobAssignment->id . '/files', 'public');

                    // Save file details to project_files table
                    $jobAssignment->files()->create([
                        'file_name' => $originalFileName,
                        'file_path' => $path, // The path returned by store()
                        'mime_type' => $fileMimeType,
                        'file_size' => $fileSize,
                        'description' => $request->input('project_file_descriptions')[$index] ?? null,
                        'uploaded_by_user_id' => Auth::id(),
                    ]);
                }
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
            ->whereNot('position_level_id', 99)
            ->whereDoesntHave('jobAssignmentPersonnel', function ($query) use ($id) {
                $query->where('job_assignment_id', $id);
            })
            ->get();
        $personnels = JobAssignmentPersonnel::where('job_assignment_id', $id)->get();

        return view('job_assignment.view', compact('job', 'personnels', 'respond', 'staff'))
            ->with('title', 'Job Requisition Form Detail')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition Form', 'Detail']);
    }

    public function new_view($id, $respond)
    {
        $notif = request('notif');

        if ($id) {
            Auth::user()->notifications()
                ->where('id', $notif)
                ->update(['read_at' => now()]);
        }

        $job = JobAssignment::find($id);
        $staff = User::whereNot('id', auth()->user()->id)
            ->whereNot('position_level_id', 99)
            ->whereDoesntHave('jobAssignmentPersonnel', function ($query) use ($id) {
                $query->where('job_assignment_id', $id);
            })
            ->get();
        $personnels = JobAssignmentPersonnel::where('job_assignment_id', $id)->get();

        return view('job_assignment.oldview', compact('job', 'personnels', 'respond', 'staff'))
            ->with('title', 'Job Requisition Form Detail')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition Form', 'Detail']);
    }

    public function generate_autonumber($prefix = 'XXX')
    {
        // Get the last inserted autonumber (if any)
        $last_autonumber = DB::table('job_assignments')
            ->whereYear('created_at', date('Y')) // Filter by year
            ->whereMonth('created_at', date('m')) // Filter by month
            ->latest('id')
            ->value('job_record_id'); // Replace with the actual column name

        if (! $last_autonumber) {
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
        $autonumber = $prefix.$padded_number;

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
                'job_assignments.job_type as job_type_name',
                'job_assignments.is_publish',
            )
                ->where('job_assignments.user_id', auth()->user()->id)
                ->whereNot('job_assignments.job_status', 3)
                ->where(function ($query) use ($now) {
                    $query->where('job_assignments.start_at', '>=', $now) // Job starts today or earlier
                        // ->where('job_assignments.end_at', '>=', $now) // Job ends today or later
                        ->orWhereDate('job_assignments.start_at', $now); // Exact match with today's date
                    // ->orWhereDate('job_assignments.end_at', $now); // Exact match with today's date
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
                    } elseif ($row->job_status == 4) {
                        return "<span class='badge bg-warning'>Recalled</span>";
                    } else {
                        return "<span class='badge bg-danger'>Rejected</span>";
                    }
                })
                ->addColumn('job_record_id', function ($row) {
                    $return = '<label class="label label-sm">'.$row->job_record_id.'</label>';
                    $color = (int) $row->is_publish == 1 ? 'primary' : 'warning';
                    $title = (int) $row->is_publish == 1 ? 'Publish' : 'Draft';
                    $return .= '<span class="badge rounded-pill text-bg-'.$color.'">'.$title.'</span>';

                    return $return;
                })
                ->addColumn('action', function ($row) {
                    return '<a href="'.route('v1.job-assignment-form.view', ['id' => $row->id, 'respond' => 'no']).'" class="edit btn btn-primary btn-sm">View</a>';
                })
                ->addColumn('date_range', function ($row) {
                    return '<span class="badge rounded-pill text-bg-info">'.$row->start_at.'</span> <span class="badge rounded-pill text-bg-warning">'.$row->end_at.'</span>';
                })
                ->rawColumns(['job_record_id', 'status', 'date_range', 'action'])
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

            // Query for ongoing or future jobs
            $data4 = JobAssignment::select(
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
                ->where('job_assignments.job_status', 3); // Jobs with no end date

            // Combine both queries with UNION
            $data = $data1
                ->union($data2)
                ->union($data3)
                ->union($data4)
                ->orderBy('created_date', 'DESC')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->job_status == 0) {
                        return "<span class='badge bg-info'>Pending</span>";
                    } elseif ($row->job_status == 1) {
                        return "<span class='badge bg-success'>Accepted</span>";
                    } elseif ($row->job_status == 4) {
                        return "<span class='badge bg-warning'>Recalled</span>";
                    } else {
                        return "<span class='badge bg-danger'>Rejected</span>";
                    }
                })
                ->addColumn('department', function ($row) {
                    return $row->department_name;
                })
                ->addColumn('action', function ($row) {
                    return '<a href="'.route('v1.job-assignment-form.view', ['id' => $row->id, 'respond' => 'no']).'" class="edit btn btn-primary btn-sm">View</a>';
                })
                ->addColumn('date_range', function ($row) {
                    return '<span class="badge rounded-pill text-bg-info">'.$row->start_at.'</span> <span class="badge rounded-pill text-bg-warning">'.$row->end_at.'</span>';
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
                'job_assignments.job_type as job_type_name',
                'job_assignments.is_publish'
            )
                ->join('job_assignment_personnels', 'job_assignments.id', '=', 'job_assignment_personnels.job_assignment_id')
                ->where('job_assignment_personnels.user_id', auth()->user()->id)
                ->whereNot('job_assignments.job_status', 3)
                ->where(function ($query) use ($now) {
                    $query->where('job_assignments.start_at', '>=', $now) // Job starts today or earlier
                        // ->where('job_assignments.end_at', '>=', $now) // Job ends today or later
                        ->orWhereDate('job_assignments.start_at', $now); // Exact match with today's date
                    // ->orWhereDate('job_assignments.end_at', $now); // Exact match with today's date
                })
                ->orderBy('job_assignments.created_at', 'DESC');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->job_status == 0) {
                        return "<span class='badge bg-info'>Pending</span>";
                    } elseif ($row->job_status == 1) {
                        return "<span class='badge bg-success'>Accepted</span>";
                    } elseif ($row->job_status == 4) {
                        return "<span class='badge bg-warning'>Recalled</span>";
                    } elseif ($row->job_status == 3) {
                        return "<span class='badge bg-danger'>Deleted</span>";
                    } else {
                        return "<span class='badge bg-danger'>Rejected</span>";
                    }
                })
                ->addColumn('action', function ($row) {
                    $label = ($row->job_status == 0) ? 'Respond' : 'View';

                    return '<a href="'.route('v1.job-assignment-form.view', ['id' => $row->id, 'respond' => 'yes']).'" class="edit btn btn-primary btn-sm">'.$label.'</a>';
                })
                ->addColumn('job_record_id', function ($row) {
                    $return = '<label class="label label-sm">'.$row->job_record_id.'</label>';
                    $color = (int) $row->is_publish == 1 ? 'primary' : 'warning';
                    $title = (int) $row->is_publish == 1 ? 'Publish' : 'Draft';
                    $return .= '<span class="badge rounded-pill text-bg-'.$color.'">'.$title.'</span>';

                    return $return;
                })
                ->addColumn('date_range', function ($row) {
                    return '<span class="badge rounded-pill text-bg-info">'.$row->start_at.'</span> <span class="badge rounded-pill text-bg-warning">'.$row->end_at.'</span>';
                })
                ->rawColumns(['job_record_id', 'status', 'date_range', 'action'])
                ->make(true);
        }
    }

    public function invitedStaff($user_id, $job_id)
    {
        $jobAssignment = JobAssignment::findOrFail($job_id);

        $exists = JobAssignmentPersonnel::where('user_id', $user_id)
            ->where('job_assignment_id', $jobAssignment->id)
            ->exists();

        if ($exists) {
            return redirect()->route('v1.job-assignment-form.view', ['id' => $job_id, 'respond' => 'yes']);
        }
        $data = [
            'user_id' => $user_id,
            'job_assignment_id' => $jobAssignment->id,
            'assignment_status' => 3,
        ];
        JobAssignmentPersonnel::create($data);

        $creator = User::find($jobAssignment->user_id);
        $member = User::find($user_id);
        $member->notify(new UserNotification(
            auth()->user()->name.' <strong>Invited</strong> you at Job ID '.$jobAssignment->job_record_id,
            'success',
            route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
        ));
        $creator->notify(new UserNotification(
            auth()->user()->name.' <strong>Invited</strong> '.$member->name.' at Job ID '.$jobAssignment->job_record_id,
            'success',
            route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'yes'])
        ));
        $data = [
            'email' => $member->email,
            'originator' => Auth()->user()->name,
            'personel' => $member->name,
            'job_record_id' => $jobAssignment->job_record_id,
            'type_job' => $jobAssignment->job_type,
            'scope_of_work' => $jobAssignment->scope_of_work,
            'start_at' => $jobAssignment->start_at,
            'end_at' => $jobAssignment->end_at,
            'is_vehicle_require' => $jobAssignment->is_vehicle_require ? 'Yes' : 'No',
            'url' => route('v1.job-assignment-form.view', ['id' => $jobAssignment->id, 'respond' => 'no']),
        ];
        $this->sendEmail($data);

        return redirect()->route('v1.job-assignment-form.view', ['id' => $job_id, 'respond' => 'yes'])->with('success', 'Personnel has been involved Successfully');
    }

    public function history()
    {
        $job_type = JobType::all();
        $users = User::whereNotIn('id', [auth()->id()])->get()->groupBy('department');
        $vehicles = Vehicle::all();
        $departments = Department::all();
        $persons = User::all();

        return view('job_assignment.history', compact('persons', 'departments', 'job_type', 'users', 'vehicles'))
            ->with('title', 'Job Requisition History')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition Form', 'Job Requisition History']);
    }

    public function updateJobAssignmentStatus(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'action' => 'required|in:publish,recall,cancel',
        ]);

        $event = JobAssignment::find($request->id); // Example: Find an event (replace with your logic)

        if (! $event) {
            return response()->json(['success' => false, 'message' => 'Event not found.']);
        }

        // Update status based on action
        if ($request->action === 'publish') {
            $event->is_publish = 1; // Published
        } elseif ($request->action === 'recall') {
            $event->job_status = 4; // recall
            $event->is_publish = 0;
        } elseif ($request->action === 'cancel') {
            $event->job_status = 3; // Canceled
        }

        $event->save();

        if ($request->action === 'recall') {
            $reset_personnels = JobAssignmentPersonnel::where('job_assignment_id', $event->id)->get();
            foreach ($reset_personnels as $reset_personnel) {
                $reset_personnel->assignment_status = 0;
                $reset_personnel->save();
            }
        }

        $personnels = JobAssignmentPersonnel::where('job_assignment_id', $event->id)->get();
        $personnelIds = $personnels->pluck('user_id')->toArray();

        if (! empty($personnelIds)) {   // ✅ Check if personnel exist

            // $event->personnel()->attach($personnelIds);
            foreach ($personnels as $personnelId) {
                $user = User::find($personnelId->user_id);
                if ($user) {  // ✅ Ensure the user exists before sending notification
                    $user->notify(new UserNotification(
                        auth()->user()->name.' was '.$request->action.'ed to Job ID '.$event->job_record_id,
                        'success',
                        route('v1.job-assignment-form.view', ['id' => $event->id, 'respond' => 'yes'])
                    ));
                }
            }
        }

        if ($request->action === 'cancel') {
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

    public function sendEmail($data)
    {
        //Mail::to($data['email'])->send(new ConfirmationMail($data));
         //SendBulkEmailJob::dispatch('teguh.susanto@hotmail.com', $data)
         ///       ->delay(now()->addSeconds(120));
         $delay = 0;
        //for ($i = 0; $i < count( $data); $i++) {
            SendBulkEmailJob::dispatch($data['email'], $data)
                ->delay(now()->addSeconds(120));
            //$delay += 5; // add 5s per email
        //}

        return response()->json(['message' => 'Vibtech Genesis Staff Portal']);
    }

    public function sendBookingEmail()
    {
        $booking = [
            'originator' => 'Teguh Susanto',
            'personel' => 'Houston Teo',
            'job_record_id' => '001',
            'type_job' => 'Testing Email',
            'scope_of_work' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',
            'start_at' => '2025-03-13',
            'end_at' => '2025-03-15',
            'is_vehicle_require' => 'No',
            'url' => 'http://localhost:8000/v1/job-assignment-form/view/8/no',
        ];
        $delay = 0;

        for ($i = 0; $i <= 10; $i++) {
            // Queue email with delay if needed
            SendBulkEmailJob::dispatch('teguh.susanto@hotmail.com', $booking)
                ->delay(now()->addSeconds($delay));

            // Increase delay every 6 emails to throttle
            if (($i + 1) % 6 === 0) {
                $delay += 15; // delay next batch by 5 seconds
            }
        }
        

        return response()->json(['message' => 'Vibtech Genesis Staff Portal']);
    }

    public function assignVehicleBooker(Request $request)
    {
        // Validate the request
        $request->validate([
            'job_id' => 'required',
        ]);

        // Get the Job Assignment
        $job = JobAssignmentPersonnel::findOrFail($request->job_id);
        $job1 = JobAssignmentPersonnel::where('job_assignment_id', $job->job_assignment_id)->get();
        foreach ($job1 as $row) {
            $row->is_booker = 0;
            $row->save();
        }

        // Assign the vehicle booker (You may need to define your own logic here)
        $job->is_booker = 1;
        $job->save();

        // Return success response
        return response()->json(['success' => 'Vehicle booker assigned successfully!']);
    }

    public function getJobsAssignmentFiles(Request $request)
    {
        // Get filters from DataTables AJAX request (or custom parameters)
        $jobId = $request->input('job_id');
        
        $query = JobAssignmentFile::query()->with(['uploadedBy']);

        if ($request->has('job_id')) {
            $query->where('job_assignment_id', $request->job_id);
        }

        

        return DataTables::eloquent($query)
            ->addColumn('file_name_link', function (JobAssignmentFile $file) {
                // Generate a clickable link to download/view the file
                $url = Storage::url($file->file_path); // Using the accessor you defined in the model
                $icon = '';
                // Add icons based on mime type
                if (\Str::contains($file->mime_type, ['pdf'])) {
                    $icon = '<i class="fas fa-file-pdf text-danger me-1"></i>';
                } elseif (\Str::contains($file->mime_type, ['word', 'document'])) {
                    $icon = '<i class="fas fa-file-word text-primary me-1"></i>';
                } elseif (\Str::contains($file->mime_type, ['excel', 'spreadsheet'])) {
                    $icon = '<i class="fas fa-file-excel text-success me-1"></i>';
                } elseif (\Str::contains($file->mime_type, ['image'])) {
                    $icon = '<i class="fas fa-file-image text-info me-1"></i>';
                } else {
                    $icon = '<i class="fas fa-file text-muted me-1"></i>';
                }

                return $file->description.'<p><small><a href="'.$url.'" target="_blank" download class="text-decoration-none">' . $icon . e($file->short_file_name) . '</a> ('.number_format($file->file_size / 1024, 2).' KB)</small></p>';
            })
            ->addColumn('uploaded_by', function ($query) {
                return $query->uploadedBy->name.'<p><small>'.$query->created_at->format('l, F d, Y \a\t h:i A').'</small></p>' ?? 'N/A';
            })
            
            ->addColumn('file_size_formatted', function (JobAssignmentFile $file) {
                // Convert bytes to KB, MB, etc.
                $bytes = $file->file_size;
                $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                $i = 0;
                while ($bytes >= 1024 && $i < count($units) - 1) {
                    $bytes /= 1024;
                    $i++;
                }
                return round($bytes, 2) . ' ' . $units[$i];
            })
            ->addColumn('action', function (JobAssignmentFile $file) {
                $buttons = '<a href="'. Storage::url($file->file_path) .'" target="_blank" download class="btn btn-sm btn-outline-info me-1" title="Download"><i class="fas fa-download"></i></a>';
                if($file->uploaded_by_user_id == auth()->user()->id){
                    $buttons .= '<button type="button" class="btn btn-sm btn-outline-danger delete-project-file-btn" 
                    data-file-id="'.$file->id.'" data-file-name="'.e($file->file_name).'" title="Delete"><i class="fas fa-trash"></i></button>';
                }
                
                return $buttons;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function initiate()
    {
        return view('job_assignment.initiate')
            ->with('title', 'View Job Requisition')
            ->with('breadcrumb', ['Home', 'Staff Task', 'Job Requisition', 'View Job Requisition']);
    }

    public function initiateBulkSend(Request $request)
    {
        $request->validate([
            'recipient_emails' => 'required|array',
            'recipient_emails.*' => 'email',
            'email_subject' => 'required|string|max:255',
            'email_body' => 'required|string',
            // Add any other validation for your email data
        ]);

        $recipientEmails = $request->input('recipient_emails');
        $totalRecipients = count($recipientEmails);

        if ($totalRecipients === 0) {
            return response()->json(['success' => false, 'message' => 'No recipients provided.'], 400);
        }

        // 1. Create a new EmailBatch record
        $emailBatch = EmailBatch::create([
            'name' => $request->input('email_subject') . ' - ' . now()->format('Y-m-d H:i:s'),
            'total_recipients' => $totalRecipients,
            'status' => 'in_progress',
            'user_id' => Auth::id(),
        ]);

        // 2. Dispatch a job for each recipient
        foreach ($recipientEmails as $email) {
            $emailData = [
                'subject' => $request->input('email_subject'),
                'body' => $request->input('email_body'),
                // Add any other data your Mailable needs
            ];

            // Dispatch the job, passing the batch ID
            SendBulkEmailJob::dispatch($email, $emailData, $emailBatch->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bulk email sending initiated.',
            'batch_id' => $emailBatch->id,
            'total_recipients' => $totalRecipients,
        ]);
    }

    /**
     * API endpoint to get the progress of an email batch.
     *
     * @param int $batchId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBatchProgress(int $batchId)
    {
        $batch = EmailBatch::find($batchId);

        if (!$batch) {
            return response()->json(['success' => false, 'message' => 'Batch not found.'], 404);
        }

        $progressPercentage = 0;
        if ($batch->total_recipients > 0) {
            $progressPercentage = round((($batch->sent_count + $batch->failed_count) / $batch->total_recipients) * 100);
        }

        return response()->json([
            'success' => true,
            'batch_id' => $batch->id,
            'total_recipients' => $batch->total_recipients,
            'sent_count' => $batch->sent_count,
            'failed_count' => $batch->failed_count,
            'status' => $batch->status,
            'progress_percentage' => min(100, $progressPercentage), // Cap at 100%
        ]);
    }
}
