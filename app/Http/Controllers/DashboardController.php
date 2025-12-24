<?php

namespace App\Http\Controllers;

use App\Models\JobAssignment;
use App\Models\JobAssignmentPersonnel;
use App\Models\VehicleBooking;
use App\Models\Project;
use App\Models\User;
use App\Models\LeaveApplication;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\ProjectStageTask;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    function initials($name)
    {
        return collect(explode(' ', trim($name)))
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');
    }
    public function index()
{
    // Define the start and end dates for the three months
    $lastMonthStart = now()->subMonth(6)->startOfMonth();
    $nextMonthEnd = now()->addMonth()->endOfMonth();

    // Use a single query for JobAssignments, filtering by the date range
    $jobAssignments = JobAssignment::join('users', 'users.id', '=', 'job_assignments.user_id')
        ->where('is_publish', 1)
        ->select(
        'job_assignments.id',
        'job_assignments.job_type',
        'job_assignments.start_at',
        'job_assignments.end_at',
        'users.name as user_name'
    )
        ->whereBetween('start_at', [$lastMonthStart, $nextMonthEnd])
        ->get();

    // Use a single query for VehicleBookings, filtering by the date range
    // Eager load the vehicle relationship to prevent N+1 queries
    $vehicleBookings = VehicleBooking::with('vehicle')
        ->select('id', 'start_at', 'end_at', 'vehicle_id')
        ->whereBetween('start_at', [$lastMonthStart, $nextMonthEnd])
        ->get();

    // Transform and combine the data into a single events array
    $events = $jobAssignments->map(function ($job) {
        return [
            'id' => $job->id,
            'url' => '',
            'title' => $job->job_type,
            'start' => $job->start_at->toDateTimeString(),
            'end' => $job->end_at->addDay()->toDateTimeString(),
            'allDay' => true,
            'extendedProps' => ['calendar' => 'Holiday'],
            'event_status' => 'JR',
            'color' => 'primary',
        ];
    })->concat($vehicleBookings->map(function ($booking) {
        return [
            'id' => $booking->id,
            'url' => '',
            'title' => $booking->vehicle->name,
            'start' => $booking->start_at->toDateTimeString(),
            'end' => $booking->end_at->addDay()->toDateTimeString(),
            'allDay' => true,
            'extendedProps' => ['calendar' => 'Business'],
            'event_status' => 'VB',
            'color' => 'success',
        ];
    }))->all();

    $currentYear = now()->year;
$nextYear = $currentYear + 1;

$publicHolidays = LeaveApplication::whereIn(
        DB::raw('YEAR(leave_date)'),
        [$currentYear, $nextYear]
    )
    ->select(
        'id',
        'leave_date as date',
        'title as name',
        'country_code as country'
    )
    ->orderBy('leave_date')
    ->get()
    ->toArray();

    $publicHolidaysxx = [
        [
            'date' => '2025-12-01',
            'name' => 'New Year Holiday',
            'country' => 'SG',
        ],
        [
            'date' => '2025-12-10',
            'name' => 'Federal Territory Day',
            'country' => 'MY',
        ],
    ];

    $holidayEvents = collect($publicHolidays)->map(function ($holiday) {
    return [
        //'id' => 'PH-' . md5($holiday['date'] . $holiday['country']),
        'id' => $holiday['id'],
        'title' => $holiday['name'],
        'start' => $holiday['date'],
        'end' => \Carbon\Carbon::parse($holiday['date'])->addDay()->toDateString(),
        'allDay' => true,

        'allDay' => true,
        'extendedProps' => ['calendar' => $holiday['country'] === 'SG' ? 'Personal' : 'Family',"category"=>$holiday['country'] === 'SG' ? 'Personal' : 'Family'],
        
        'event_status' => 'PH',
        'color' => 'success',
    ];
});

$jobEvents = $jobAssignments->map(function ($job) {
    return [
        'id' => $job->id,
        'url' => '',
        'title' => $this->initials($job->user_name) .' - '. $job->job_type,
        'start' => $job->start_at->toDateString(),
        'end' => $job->end_at->addDay()->toDateString(),
        'allDay' => true,
        'event_status' => 'JR',
        'color' => '#0d6efd',
        'extendedProps' => [
            'calendar' => 'Holiday',
            'type' => 'job',
        ],
    ];
});
$vehicleEvents = $vehicleBookings->map(function ($booking) {
    return [
        'id' => $booking->id,
        'url' => '',
        'title' => $booking->vehicle->name,
        'start' => $booking->start_at->toDateString(),
        'end' => $booking->end_at->addDay()->toDateString(),
        'allDay' => true,
        'event_status' => 'VB',
        'color' => '#198754',
        'extendedProps' => [
            'calendar' => 'Business',
            'type' => 'vehicle',
        ],
    ];
});

$events = $jobEvents
    ->concat($vehicleEvents)
    ->concat($holidayEvents)
    ->values()
    ->all();





    $databaseNotifications = DatabaseNotification::where('notifiable_id', Auth::id())
            // Filter notifications that match specific group message types
            ->where(function ($query) {
                // IMPORTANT: The data column is a JSON string, so we search for substrings.
                // It's better to store a 'type' column, but this adheres to the current structure.
                $query->where('data', 'LIKE', '%management-memo%')
                      ->orWhere('data', 'LIKE', '%employee-handbook%')
                      ->orWhere('data', 'LIKE', '%job-assignment-form%')
                      ->orWhere('data', 'LIKE', '%submit-claim%');
            })
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();

        // --- 2. Configuration Maps ---
        
        // This map is used to determine the group name and color based on the URL/keyword.
        // The key is a unique identifier (like a segment of the notification URL or a type identifier).
        $notificationGroupMap = [
            'management-memo'       => ['group_name' => 'Management Memo', 'bgcolor' => 'bg-warning'],
            'employee-handbook'     => ['group_name' => 'Employee Handbook', 'bgcolor' => 'bg-secondary'],
            'success'   => ['group_name' => 'Job Requisition', 'bgcolor' => 'bg-primary'],
            'submit-claim'         => ['group_name' => 'Submit Claim', 'bgcolor' => 'bg-info'],
        ];

        // --- 3. Process Notifications to extract data and assign properties ---
        $groupNotifications = $databaseNotifications->map(function ($notification) use ($notificationGroupMap) {
            
            // a. Parse the JSON data from the 'data' column
            $data = (array) $notification->data; 
            
            // b. Attempt to find the correct group based on the message content (or type)
            $groupType = 'default';
            foreach ($notificationGroupMap as $keyword => $map) {
                // Assuming the unique keyword (e.g., 'management-memo') exists in the message/data
                if (str_contains($data['type'] ?? '', $keyword)) {
                    $groupType = $keyword;
                    break;
                }
            }
            
            $groupConfig = $notificationGroupMap[$groupType] ?? ['group_name' => 'General', 'bgcolor' => 'bg-info'];

            // c. Construct the final object structure required by the JavaScript
            return [
                'id'         => $notification->id,
                'group_name' => $groupConfig['group_name'], // e.g., "Job Requisition"
                'message'    => $data['message'] ?? 'Notification details missing.', // The actual content of the notification
                'time'       => $notification->created_at->diffForHumans(), // Time elapsed since creation
                'url'        => $data['url'] ?? '#', // The destination URL
                'bgcolor'    => $groupConfig['bgcolor'], // e.g., "bg-primary"
            ];

        })->values()->toArray();

    return view('dashboard.dashboard', compact('events','groupNotifications'))->with('title', 'Staff Calendar')->with('breadcrumb', ['Home', 'Dashboard']);
}
    public function index1()
    {

        $events = [];
        $jobs1 = JobAssignment::select('*', \DB::raw("DATE_FORMAT(start_at,'%Y%m%d') as tgl"))
            ->where('is_publish', 1)
            ->where(function ($query) {
                $query->whereRaw("DATE_FORMAT(start_at,'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')
                          AND DATE_FORMAT(end_at,'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')")
                    ->orWhereRaw("DATE_FORMAT(start_at,'%Y%m%d') <= DATE_FORMAT(NOW(),'%Y%m%d')
                            AND DATE_FORMAT(end_at,'%Y%m%d') >= DATE_FORMAT(NOW(),'%Y%m%d')")
                    ->orWhereRaw("DATE_FORMAT(start_at,'%Y%m%d') >= DATE_FORMAT(NOW(),'%Y%m%d')
                            AND DATE_FORMAT(end_at,'%Y%m%d') >= DATE_FORMAT(NOW(),'%Y%m%d')");
            })
            ->get();
        $jobs = JobAssignment::select('*', \DB::raw("DATE_FORMAT(start_at,'%Y%m%d') as tgl"))
            ->where('is_publish', 1)
            ->get();
        foreach ($jobs as $row) {
            $event = [
                'id' => $row->id, // Assuming 'id' exists
                'url' => '', // You can set this if needed
                'title' => $row->job_type, // Assuming 'title' exists
                'start' => (new DateTime($row->start_at))->format('Y-m-d H:i:s'), // Format as string
                'end' => (new DateTime($row->end_at))->modify('+1 day')->format('Y-m-d H:i:s'), // Format as string
                'allDay' => true, // Assuming 'all_day' exists
                'extendedProps' => ['calendar' => 'Holiday'], // Assuming 'extendedProps' exists, or an empty array
                'event_status' => 'JR',
                'color' => 'primary',
            ];

            $events[] = $event;
        }

        $bookings1 = VehicleBooking::where(function ($query) {
            $query->whereRaw("DATE_FORMAT(start_at,'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')
                              AND DATE_FORMAT(end_at,'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')")
                ->orWhereRaw("DATE_FORMAT(start_at,'%Y%m%d') <= DATE_FORMAT(NOW(),'%Y%m%d')
                                AND DATE_FORMAT(end_at,'%Y%m%d') >= DATE_FORMAT(NOW(),'%Y%m%d')")
                ->orWhereRaw("DATE_FORMAT(start_at,'%Y%m%d') >= DATE_FORMAT(NOW(),'%Y%m%d')
                                AND DATE_FORMAT(end_at,'%Y%m%d') >= DATE_FORMAT(NOW(),'%Y%m%d')");
        })
            ->get();

        $bookings = VehicleBooking::all();

        foreach ($bookings as $row) {
            $event = [
                'id' => $row->id, // Assuming 'id' exists
                'url' => '', // You can set this if needed
                'title' => $row->vehicle->name, // Assuming 'title' exists
                'start' => (new DateTime($row->start_at))->format('Y-m-d H:i:s'), // Format as string
                'end' => (new DateTime($row->end_at))->modify('+1 day')->format('Y-m-d H:i:s'), // Format as string
                'allDay' => true, // Assuming 'all_day' exists
                'extendedProps' => ['calendar' => 'Business'], // Assuming 'extendedProps' exists, or an empty array
                'event_status' => 'VB',
                'color' => 'success',
            ];

            $events[] = $event;
        }

        return view('dashboard.dashboard', compact('events'))->with('title', 'Staff Calendar')->with('breadcrumb', ['Home', 'Dashboard']);
    }

    public function getNotifications()
    {
        $notifications = Auth::user()->notifications->take(5); // Get latest 5 notifications

        return response()->json($notifications);
    }

    public function getEvents()
    {
        $data = JobAssignment::where('is_publish', 1)
            ->whereDate('start_at', '>=', Carbon::today()) // Replace 'job_date' with the actual date column name
            ->get();
        $arr = [];
        foreach ($data as $row) {
            $calendarType = 'Business';
            $arr[] = [
                'id' => $row->id,
                'url' => '',
                'title' => $row->job_type,
                'start' => $row->start_at,
                'end' => $row->end_at,
                'allDay' => '!1',
                'extendedProps' => ['calendar' => $calendarType],
            ];
        }

        return json_encode($arr);

    }

    public function getEventsByDate($eventAt)
    {
        $filterDate = Carbon::parse($eventAt); // Parse the date using Carbon
        $filterDate = substr($filterDate, 0, 10);
        $now = Carbon::now(); // Get the current datetime
        $arr = [];

        if ($filterDate) {
            $results = JobAssignment::where('is_publish', 1)
                ->where(function ($query) use ($filterDate, $now) {
                    $query->where('start_at', '<=', $filterDate) // Start date is today or earlier
                        ->where('end_at', '>=', $filterDate) // End date is today or later
                        ->where('end_at', '>=', $now->subDays(1)); // Exclude past jobs
                })
                ->orWhere(function ($query) use ($filterDate, $now) {
                    $query->whereNull('start_at')
                        ->where('end_at', '>=', $filterDate)
                        ->where('end_at', '>=', $now->subDays(1)); // Exclude past jobs
                })
                ->orWhere(function ($query) use ($filterDate, $now) {
                    $query->where('start_at', '<=', $filterDate)
                        ->whereNull('end_at')
                        ->where('start_at', '>=', $now); // Ensure job hasn't ended
                })
                ->orWhere(function ($query) use ($now) {
                    $query->whereNull('start_at')
                        ->whereNull('end_at')
                        ->where('created_at', '>=', $now); // Exclude old records
                })
                ->get();

            foreach ($results as $row) {
                $personels = JobAssignmentPersonnel::where('job_assignment_id', $row->id)->get();
                $colorClasses = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'];
                $persons = '';

                $persons = '';
                $index = 0;
                foreach ($personels as $index => $person) {
                    $randomColor = $colorClasses[array_rand($colorClasses)]; // Pick a random color
                    $persons .= "<span class='badge rounded-pill text-bg-{$randomColor}'>".$person->user->name.'</span> ';

                    // Add a line break after every 2 persons
                    if (($index + 1) % 2 == 0) {
                        $persons .= '<br>';
                    }
                }
                $persons .= '';
                $arr[] = [
                    'id' => $row->id,
                    'title' => "<div class='callout-event'><label class='text-success'>".$row->job_type.'</label><br>'.$persons.'</div>',
                    'is_vehicle_require' => $row->is_vehicle_require,
                ];
            }

            $bookings = VehicleBooking::where('start_at', '<=', $filterDate.' 23:59:59')
                ->where('end_at', '>=', $filterDate.' 00:00:00')
                ->get();

            foreach ($bookings as $row) {
                $arr[] = [
                    'id' => $row->id,
                    'title' => "<div class='callout'><label class='text-primary' style='font-size: 0.8em;'>".$row->purposes."</label>
                    <p><span class='badge rounded-pill text-bg-{$colorClasses[array_rand($colorClasses)]}' style='font-size: 0.7em;'>".$row->vehicle->name.'</span></p></div>',
                    'is_vehicle_require' => 99,
                ];
            }
        }

        return json_encode($arr);
    }

    /**
     * Handle autocomplete search requests.
     * Returns categorized results.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function autocomplete(Request $request)
    {
        $query = $request->input('query');
        $results = [];

        if (empty($query)) {
            return response()->json(['success' => true, 'results' => []]);
        }

        // Search Projects
        $projects = Project::where('name', 'like', '%' . $query . '%')
                           ->orWhere('description', 'like', '%' . $query . '%')
                           ->limit(5)
                           ->whereNot('data_status',0)
                           ->get(['id', 'name']); // Select only necessary fields
        $results['projects'] = $projects->map(function ($project) {
            return [
                'name' => $project->name,
                'url' => route('v1.project-management.detail', ['project' => $project->obfuscated_id]), // Adjust to your project detail route
                'meta_info' => 'Project'
            ];
        });

        // Search Tasks
        $tasks = ProjectStageTask::where('name', 'like', '%' . $query . '%')
                                 ->orWhere('description', 'like', '%' . $query . '%')
                                 ->with('projectStage.project') // Eager load project to get project ID
                                 ->limit(5)
                                 ->whereNot('data_status',0)
                                 ->get(['id', 'name', 'project_stage_id']); // Select necessary fields
        $results['tasks'] = $tasks->map(function ($task) {
            return [
                'name' => $task->name,
                'url' => route('v1.project-management.detail', ['project' => $task->projectStage->project->obfuscated_id]),
                'meta_info' => 'Task in ' . ($task->projectStage->project->name ?? 'N/A')
            ];
        });


        // Search Users
        $users = User::where('name', 'like', '%' . $query . '%')
                     ->orWhere('email', 'like', '%' . $query . '%')
                     ->limit(5)
                     ->get(['id', 'name', 'email']); // Select necessary fields
        $results['users'] = $users->map(function ($user) {
            return [
                'name' => $user->name,
                'email' => $user->email, // Display email for users
                'url' => route('users.profile', $user->id), // Adjust to your user profile route
                'meta_info' => $user->email
            ];
        });

        // Search Job Requisitions (Example)
        if (class_exists(JobAssignment::class)) { // Check if the model exists
             $jobRequisitions = JobAssignment::where('job_type', 'like', '%' . $query . '%')
                                              ->orWhere('business_name', 'like', '%' . $query . '%')
                                              ->limit(5)
                                              ->whereNot('job_status',0)
                                              ->get(['id', 'job_type']);
             $results['job_requisitions'] = $jobRequisitions->map(function ($jr) {
                 return [
                     'name' => $jr->job_type,
                     'url' => route('v1.job-assignment-form.view', ['id'=>$jr->id,'respond'=>'no']), // Adjust to your JR detail route
                     'meta_info' => 'Job Requisition'
                 ];
                 
             });
        }


        return response()->json(['success' => true, 'results' => $results]);
    }

    /**
     * Display a comprehensive search results page.
     * (Optional: if you have a dedicated results page)
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function searchResult(Request $request)
    {
        $query = $request->input('query');
        $results = [];

        if (!empty($query)) {
            // Re-run the same search logic, but potentially fetch more results
            // You might want to paginate these results on the full page
            $results['projects'] = Project::where('name', 'like', '%' . $query . '%')
                                         ->orWhere('description', 'like', '%' . $query . '%')
                                         ->whereNot('data_status',0)
                                         ->paginate(10, ['*'], 'projects_page');

            $results['tasks'] = ProjectStageTask::where('name', 'like', '%' . $query . '%')
                                               ->orWhere('description', 'like', '%' . $query . '%')
                                               ->with('projectStage.project')
                                               ->whereNot('data_status',0)
                                               ->paginate(10, ['*'], 'tasks_page');

            $results['users'] = User::where('name', 'like', '%' . $query . '%')
                                    ->orWhere('email', 'like', '%' . $query . '%')
                                    ->paginate(10, ['*'], 'users_page');

            // Add other search types
            if (class_exists(JobAssignment::class)) {
                $results['job_requisitions'] = JobAssignment::where('job_type', 'like', '%' . $query . '%')
                                                             ->orWhere('business_name', 'like', '%' . $query . '%')
                                                             ->whereNot('job_status',0)
                                                             ->paginate(10, ['*'], 'job_requisitions_page');
            }
        }

        return view('search.results', compact('query', 'results'));
    }

    /**
     * Fetch events for a specific date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEventsByDateModal(Request $request)
    {
        $date = $request->query('date'); // Get the date from the query parameter

       

        try {
            $eventAt = $request->query('date'); // Get the date from the query parameter (e.g., 'YYYY-MM-DD')

        if (empty($eventAt)) {
            return response()->json(['success' => false, 'message' => 'Date parameter is required.'], 400);
        }

        // Parse the date using Carbon and ensure it's a date-only string for comparisons
        $filterDate = Carbon::parse($eventAt)->format('Y-m-d');
        $now = Carbon::now(); // Get the current datetime for comparisons
        $formattedEvents = []; // This will hold our final array of events

        // --- Fetch Job Assignments ---
        $jobAssignments = JobAssignment::where('is_publish', 1)
            ->with(['personnel']) // Eager load personnel and their users to avoid N+1 queries
            ->where(function ($query) use ($filterDate, $now) {
                // Condition 1: Job spans across or includes the filterDate
                $query->where('start_at', '<=', $filterDate . ' 23:59:59') // Job starts on or before filterDate
                      ->where('end_at', '>=', $filterDate . ' 00:00:00'); // Job ends on or after filterDate
                      // ->where('end_at', '>=', $now->subDays(1)); // Exclude past jobs relative to 'now' (this might be too strict)
            })
            // Or Condition 2: Job with null start_at, but ends on or after filterDate
            ->orWhere(function ($query) use ($filterDate, $now) {
                $query->whereNull('start_at')
                      ->where('end_at', '>=', $filterDate . ' 00:00:00');
                      // ->where('end_at', '>=', $now->subDays(1)); // Exclude past jobs
            })
            // Or Condition 3: Job with null end_at, but starts on or before filterDate
            ->orWhere(function ($query) use ($filterDate, $now) {
                $query->where('start_at', '<=', $filterDate . ' 23:59:59')
                      ->whereNull('end_at');
                      // ->where('start_at', '>=', $now->startOfDay()); // Ensure job hasn't started in the future
            })
            // Or Condition 4: Job with both start_at and end_at null, but created recently
            ->orWhere(function ($query) use ($filterDate, $now) {
                $query->whereNull('start_at')
                      ->whereNull('end_at')
                      ->where('created_at', '>=', $now->subDays(30)); // Example: created in last 30 days if no dates
            })
            ->get();

        $colorClasses = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'];
        $eventID=null;
        foreach ($jobAssignments as $job) {
            if($eventID<>$job->id){
            $personsHtml = '';
            // Loop through eager loaded personnel
            foreach ($job->personnel as $index => $person) {
                
                $randomColor = $colorClasses[array_rand($colorClasses)];
                $personsHtml .= "<span class='badge rounded-pill text-bg-{$randomColor}'>".$person->name.'</span> ';

                if (($index + 1) % 2 == 0) { // Add a line break after every 2 persons
                    $personsHtml .= '<br>';
                }
            }

            // Determine time range for Job Assignment
            $jobStartTime = $job->start_at ? $job->start_at->format('d M Y') : 'N/A';
            $jobEndTime = $job->end_at ? $job->end_at->format('d M Y') : 'N/A';
            $jobTimeRange = ($job->start_at || $job->end_at) ? "{$jobStartTime} - {$jobEndTime}" : 'Full Day';

            $formattedEvents[] = [
                'id' => 'job_' . $job->id, // Prefix ID to distinguish from bookings
                'real_id'=>$job->id,
                'title' => "<div class='callout-event'><label class='text-success'>".$job->job_type.'</label><br>'.$personsHtml.'</div>',
                'description' => $job->description ?? 'No description.', // Assuming description exists
                'time' => $jobTimeRange,
                'type' => 'Job Assignment',
                'is_vehicle_require' => $job->is_vehicle_require, // Keep this if needed
            ];
             }
            $eventID = $job->id;
        }

        // --- Fetch Vehicle Bookings ---
        $vehicleBookings = VehicleBooking::where('start_at', '<=', $filterDate . ' 23:59:59')
            ->where('end_at', '>=', $filterDate . ' 00:00:00')
            ->with('vehicle') // Eager load vehicle details
            ->get();
        $eventID=null;
        foreach ($vehicleBookings as $booking) {
            if($eventID<>$booking->id){
            $vehicleName = $booking->vehicle->name ?? 'Unknown Vehicle';
            $randomColor = $colorClasses[array_rand($colorClasses)]; // Get a random color for vehicle badge

            // Determine time range for Vehicle Booking
            $bookingStartTime = $booking->start_at ? Carbon::parse($booking->start_at)->format('d M Y H:i') : 'N/A';
            $bookingEndTime = $booking->end_at ? Carbon::parse($booking->end_at)->format('d M Y H:i') : 'N/A';
            $bookingTimeRange = ($booking->start_at || $booking->end_at) ? "{$bookingStartTime} - {$bookingEndTime}" : 'Full Day';
                $formattedEvents[] = [
                    
                    'id' => 'booking_' . $booking->id, // Prefix ID to distinguish
                    'title' => "<div class='callout'><label class='text-primary' style='font-size: 0.8em;'>".$booking->purposes."</label>
                                <p><span class='badge rounded-pill text-bg-{$randomColor}' style='font-size: 0.7em;'>".$vehicleName.'</span></p></div>',
                    'description' => $booking->notes ?? 'No notes.', // Assuming notes exists on VehicleBooking
                    'time' => $bookingTimeRange,
                    'type' => 'Vehicle Booking',
                    'is_vehicle_require' => 99, // Keep this if needed
                ];
            }
            $eventID = $booking->id;
                
        }

        // Sort events by time if possible, or by type
        usort($formattedEvents, function($a, $b) {
            // Simple sorting by time string, assuming H:i format or 'N/A'/'Full Day'
            // You might need more robust sorting if times are complex
            $timeA = $a['time'] === 'N/A' || $a['time'] === 'Full Day' ? '00:00' : substr($a['time'], 0, 5);
            $timeB = $b['time'] === 'N/A' || $b['time'] === 'Full Day' ? '00:00' : substr($b['time'], 0, 5);
            return strcmp($timeA, $timeB);
        });


            return response()->json(['success' => true, 'events' => $formattedEvents]);

        } catch (\Exception $e) {
            \Log::error("Error fetching events for date {$date}: {$e->getMessage()}", ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching events.'.$e->getMessage()], 500);
        }
    }

    public function eventHistory(Request $request) {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        // 1. Ambil data Job Assignment
        $jobs = JobAssignment::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => "Job: " . $item->job_type, // sesuaikan kolom
                    'start' => $item->created_at,
                    'type' => 'JR',
                    'url' => route('v1.job-assignment-form.view', ['id'=>$item->id,'respond'=>'no']), // sesuaikan route
                    'badge_color' => 'bg-label-success'
                ];
            });

        // 2. Ambil data Vehicle Booking
        $bookings = VehicleBooking::whereMonth('start_at', $month)
            ->whereYear('start_at', $year)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => "Booking: " . $item->vehicle->name,
                    'start' => $item->start_at,
                    'type' => 'VB',
                    'url' => '#', 
                    'badge_color' => 'bg-label-info'
                ];
            });

        // 3. Ambil data Leave Application
        $leaves = LeaveApplication::whereMonth('leave_date', $month)
            ->whereYear('leave_date', $year)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => "Leave: " . $item->employee_name,
                    'start' => $item->start_date,
                    'type' => 'LA',
                    'url' => '#',
                    'badge_color' => 'bg-label-warning'
                ];
            });

        // Gabungkan semua dan urutkan berdasarkan tanggal terbaru
        $allEvents = $jobs->concat($bookings)->concat($leaves)->sortByDesc('start');

        return view('dashboard.history', [
            'title' => 'Event History',
            'breadcrumb' => ['Dashboard', 'Event History'],
            'events' => $allEvents,
            'selectedMonth' => $month,
            'selectedYear' => $year
        ]);
    }

}
