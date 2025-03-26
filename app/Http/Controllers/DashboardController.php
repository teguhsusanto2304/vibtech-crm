<?php

namespace App\Http\Controllers;

use App\Models\JobAssignment;
use App\Models\JobAssignmentPersonnel;
use App\Models\VehicleBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Illuminate\Http\Request;



class DashboardController extends Controller
{
    public function index2(Request $request)
    {
        // Get current month & year or from request
        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));

        // Get first and last day of the month
        $firstDay = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startOfWeek = $firstDay->startOfWeek(Carbon::SUNDAY)->format('Y-m-d'); // Start from Sunday

        // Generate an array of dates
        $calendar = [];
        $date = Carbon::parse($startOfWeek);

        for ($i = 0; $i < 42; $i++) { // 6 weeks x 7 days
            $calendar[] = [
                'date' => $date->copy(),
                'isCurrentMonth' => $date->month == $month,
            ];
            $date->addDay();
        }
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        // Fetch events and group them by date
$events = VehicleBooking::select('purposes', 'start_at', 'end_at')->get()->flatMap(function ($event) {
    $dates = [];
    $start = Carbon::parse($event->start_at);
    $end = Carbon::parse($event->end_at);

    for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
        $dates[$date->toDateString()][] = $event->purposes; // Store multiple events in an array
    }

    return $dates;
});

// Predefined colors for different events
$availableColors = ['#FF5733', '#33FF57', '#3357FF', '#F39C12', '#8E44AD', '#1ABC9C', '#E74C3C'];

// Assign random colors for unique events
$eventColors = \Cache::rememberForever('event_colors', function () use ($events, $availableColors) {
    $assignedColors = [];
    foreach ($events as $date => $purposes) {
        foreach ($purposes as $purpose) {
            if (!isset($assignedColors[$purpose])) {
                $assignedColors[$purpose] = $availableColors[array_rand($availableColors)];
            }
        }
    }
    return $assignedColors;
});

// Generate the calendar array
$calendar = [];

for ($date = $startDate->copy()->startOfWeek(); $date <= $endDate->copy()->endOfWeek(); $date->addDay()) {
    $eventNames = $events[$date->toDateString()] ?? [];
    $calendar[] = [
        'date' => $date->copy(),
        'isCurrentMonth' => $date->month == $month,
        'events' => $eventNames, // Store all events for the date
        'colors' => array_map(fn($event) => $eventColors[$event] ?? '#000', $eventNames),
    ];
}

        return view('dashboard.calendar', compact('calendar', 'month', 'year','events'));
    }
    public function index()
    {

        $events = [];
        $jobs = JobAssignment::select('*', \DB::raw("DATE_FORMAT(start_at,'%Y%m%d') as tgl"))
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
        foreach ($jobs as $row):
            $startAt = (new DateTime($row->start_at))->format('Y-m-d');
            $endAt = (new DateTime($row->end_at))->format('Y-m-d');

            if ($startAt === $endAt) {
                $endAt = null;
            } else {
                $endAt = (new DateTime($row->end_at))->modify('+1 day')->format('Y-m-d H:i:s'); // Format as string
            }
                        $event = [
                'id' => $row->id, // Assuming 'id' exists
                'url' => "", // You can set this if needed
                'title' => $row->job_type, // Assuming 'title' exists
                'start' => (new DateTime($row->start_at))->format('Y-m-d H:i:s'), // Format as string
                'end' => $endAt,
                'allDay' => true, // Assuming 'all_day' exists
                'extendedProps' => ['calendar' => "Holiday"], // Assuming 'extendedProps' exists, or an empty array
                'event_status' => 'JR',
                'color'=>'primary'
            ];

            $events[] = $event;
        endforeach;

        $bookings = VehicleBooking::where(function ($query) {
            $query->whereRaw("DATE_FORMAT(start_at,'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')
                              AND DATE_FORMAT(end_at,'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')")
                  ->orWhereRaw("DATE_FORMAT(start_at,'%Y%m%d') <= DATE_FORMAT(NOW(),'%Y%m%d')
                                AND DATE_FORMAT(end_at,'%Y%m%d') >= DATE_FORMAT(NOW(),'%Y%m%d')")
                  ->orWhereRaw("DATE_FORMAT(start_at,'%Y%m%d') >= DATE_FORMAT(NOW(),'%Y%m%d')
                                AND DATE_FORMAT(end_at,'%Y%m%d') >= DATE_FORMAT(NOW(),'%Y%m%d')");
        })
            ->get();

        foreach ($bookings as $row):
            $startAt = (new DateTime($row->start_at))->format('Y-m-d');
            $endAt = (new DateTime($row->end_at))->format('Y-m-d');

            if ($startAt === $endAt) {
                $endAt = null;
            } else {
                $endAt = (new DateTime($row->end_at))->modify('+1 day')->format('Y-m-d H:i:s'); // Format as string
            }

            $event = [
                'id' =>  $row->id, // Assuming 'id' exists
                'url' => "", // You can set this if needed
                'title' => $row->vehicle->name, // Assuming 'title' exists
                'start' => (new DateTime($row->start_at))->format('Y-m-d H:i:s'), // Format as string
                'end' => $endAt, // Format as string
                'allDay' => true, // Assuming 'all_day' exists
                'extendedProps' => ['calendar' => "Business"], // Assuming 'extendedProps' exists, or an empty array
                'event_status' => 'VB',
                'color'=>'success'
            ];

            $events[] = $event;
        endforeach;


        return view('dashboard.dashboard', compact('events'))->with('title', 'Dashboard')->with('breadcrumb', ['Home', 'Dashboard']);
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
        foreach ($data as $row):
            $calendarType = "Business";
            $arr[] = [
                'id' => $row->id,
                'url' => '',
                'title' => $row->job_type,
                'start' => $row->start_at,
                'end' => $row->end_at,
                'allDay' => '!1',
                'extendedProps' => ['calendar' => $calendarType]
            ];
        endforeach;
        return json_encode($arr);

    }

    public function getEventsByDate($eventAt)
    {
        $filterDate = Carbon::parse($eventAt); // Parse the date using Carbon
        $filterDate = substr($filterDate,0,10);
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
                $personels = JobAssignmentPersonnel::where('job_assignment_id',$row->id)->get();
                $colorClasses = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'];
                $persons = "";

                $persons = '';
                $index =0;
                foreach ($personels as $index => $person) {
                    $randomColor = $colorClasses[array_rand($colorClasses)]; // Pick a random color
                    $persons .= "<span class='badge rounded-pill text-bg-{$randomColor}'>" . $person->user->name . "</span> ";

                    // Add a line break after every 2 persons
                    if (($index + 1) % 2 == 0) {
                        $persons .= '<br>';
                    }
                }
                $persons .= '';
                $arr[] = [
                    'id' => $row->id,
                    'title' => "<div class='callout-event'><label class='text-success'>".$row->job_type."</label><br>".$persons."</div>",
                    'is_vehicle_require' => $row->is_vehicle_require
                ];
            }

            $bookings = VehicleBooking::where('start_at', '<=', $filterDate . ' 23:59:59')
            ->where('end_at', '>=', $filterDate . ' 00:00:00')
            ->get();

            foreach ($bookings as $row) {
                $arr[] = [
                    'id' =>  $row->id,
                    'title' => "<div class='callout'><label class='text-primary' style='font-size: 0.8em;'>".$row->purposes ."</label>
                    <p><span class='badge rounded-pill text-bg-{$colorClasses[array_rand($colorClasses)]}' style='font-size: 0.7em;'>" . $row->vehicle->name . "</span></p></div>",
                    'is_vehicle_require' => 99
                ];
            }
        }

        return json_encode($arr);
    }

}

