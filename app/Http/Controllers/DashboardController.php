<?php

namespace App\Http\Controllers;

use App\Models\JobAssignment;
use App\Models\JobAssignmentPersonnel;
use App\Models\VehicleBooking;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectStageTask;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
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

}
