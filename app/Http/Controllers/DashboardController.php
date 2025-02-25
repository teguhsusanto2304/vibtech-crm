<?php

namespace App\Http\Controllers;

use App\Models\JobAssignment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard')->with('title', 'Dashboard')->with('breadcrumb', ['Home', 'Dashboard']);
    }

    public function getEvents()
    {
        $data = JobAssignment::where('job_status', 1)
        ->whereDate('start_at', '>=', Carbon::today()) // Replace 'job_date' with the actual date column name
        ->get();
        $arr = [];
        foreach ($data as $row):
            $calendarType = "Business";
            $arr[] = [
                'id' => $row->id,
                'url' => '',
                'title' => $row->scope_of_work,
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
        $filterDate = $eventAt; // Get the single date parameter
        $arr = [];

        if ($filterDate) {
            $filterDate = Carbon::parse($filterDate); // Parse the date using Carbon

            $results = JobAssignment::where('job_status', 1)
            ->where(function ($query) use ($filterDate) {
                $query->where('start_at', '<=', $filterDate) // start_at is before or on the filter date
                    ->where('end_at', '>=', $filterDate); // end_at is after or on the filter date
            })
                ->orWhere(function ($query) use ($filterDate) { // Handle cases where start_at is NULL
                    $query->whereNull('start_at')
                        ->where('end_at', '>=', $filterDate);
                })
                ->orWhere(function ($query) use ($filterDate) { // Handle cases where end_at is NULL
                    $query->where('start_at', '<=', $filterDate)
                        ->whereNull('end_at');
                })
                ->orWhere(function ($query) use ($filterDate) { // Handle cases where both start_at and end_at is NULL
                    $query->whereNull('start_at')
                        ->whereNull('end_at');
                })
                ->get();
            $arr = [];
            foreach ($results as $row):
                $arr[] = [
                    'id' => $row->id,
                    'title' => $row->scope_of_work
                ];
            endforeach;


        }
        return json_encode($arr);

    }
}

