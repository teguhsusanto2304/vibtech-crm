<?php

namespace App\Http\Controllers;

use App\Models\JobAssignment;
use App\Models\JobAssignmentPersonnel;
use App\Models\VehicleBooking;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;

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
}
