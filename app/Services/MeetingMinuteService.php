<?php
namespace App\Services;

use App\Models\MeetingMinute;
use App\Models\MeetingAttendee;
use App\Models\User; // Assuming User model for staff
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Helpers\IdObfuscator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables; // Import Yajra DataTables

class MeetingMinuteService 
{
    /**
     * Display the form to record new meeting minutes.
     * This method is publicly accessible.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $users = User::orderBy('name')->get(); // Get all users/staff for attendee selection
        $breadcrumb = ['Dashboard', 'Meeting Minutes', 'Record New'];
        $title = 'Record New Meeting Minutes';

        return view('meeting_minutes.create', compact('users', 'breadcrumb', 'title'));
    }

    /**
     * Store a newly created meeting minute in storage.
     * This method requires authentication.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Ensure user is authenticated to submit minutes
        

        try {
            DB::beginTransaction();

            // 1. Validate main meeting details
            $validatedMeetingData = $request->validate([
                'topic' => 'required|string|max:255',
                'meeting_date' => 'required|date|before_or_equal:today',
                'start_time' => 'required|date_format:H:i', // Expects HH:MM format
                'end_time' => 'required|date_format:H:i|after:start_time',
                'attendees' => 'nullable|array', // Array of user_ids
                'attendees.*.user_id' => 'required|exists:users,id', // Each attendee must have a valid user_id
                'attendees.*.speaker_notes' => 'nullable|string|max:1000', // Speaker notes for each attendee
            ]);

            // Combine date with time for Carbon parsing if needed, though 'time' cast handles it.
            // For saving to DB, 'date' and 'time' types are fine.

            // 2. Create MeetingMinute record
            $meetingMinute = MeetingMinute::create([
                'topic' => $validatedMeetingData['topic'],
                'meeting_date' => $validatedMeetingData['meeting_date'],
                'start_time' => $validatedMeetingData['start_time'],
                'end_time' => $validatedMeetingData['end_time'],
                'saved_by_user_id' => auth()->user()->id, // Get current authenticated user's ID
            ]);

            // 3. Save attendees and their notes
            if (isset($validatedMeetingData['attendees']) && is_array($validatedMeetingData['attendees'])) {
                foreach ($validatedMeetingData['attendees'] as $attendeeData) {
                    MeetingAttendee::create([
                        'meeting_minute_id' => $meetingMinute->id,
                        'user_id' => $attendeeData['user_id'],
                        'speaker_notes' => $attendeeData['speaker_notes'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return Response::json(['message' => 'Meeting minutes saved successfully!', 'meeting_id' => $meetingMinute->id], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return Response::json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            \Log::error('Error saving meeting minutes: ' . $e->getMessage(), ['exception' => $e]);
            return Response::json(['message' => 'An unexpected error occurred while saving minutes.'], 500);
        }
    }

    /**
     * Get meeting minutes data for DataTables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMeetingMinutesData(Request $request)
    {
        $query = MeetingMinute::query()
            ->with(['attendees.user', 'savedBy']) // Eager load attendees (and their users) and savedBy user
            ->orderBy('meeting_date', 'desc')
            ->orderBy('start_time', 'desc');
        // NEW: Apply month filter
        if ($request->filled('month')) {
            $month = (int) $request->input('month');
            $query->whereMonth('meeting_date', $month);
        }

        // NEW: Apply year filter
        if ($request->filled('year')) {
            $year = (int) $request->input('year');
            $query->whereYear('meeting_date', $year);
        }
        

        return DataTables::eloquent($query)
            ->addIndexColumn() // Adds a serial number column
            ->addColumn('meeting_topic', function (MeetingMinute $minute) {
                return $minute->topic;
            })
            ->addColumn('meeting_date_formatted', function (MeetingMinute $minute) {
                return $minute->meeting_date->format('d M Y');
            })
            ->addColumn('meeting_time_range', function (MeetingMinute $minute) {
                return $minute->start_time->format('H:i') . ' - ' . $minute->end_time->format('H:i');
            })
            ->addColumn('attendees_count', function (MeetingMinute $minute) {
                return $minute->attendees->count();
            })
            ->addColumn('attendees_list', function (MeetingMinute $minute) {
                // Display a comma-separated list of attendee names
                $attendeeNames = $minute->attendees->map(function ($attendee) {
                    return $attendee->user->name ?? 'N/A';
                })->implode(', ');
                return '<span title="'.$attendeeNames.'">'.$minute->attendees->count().' Attendees</span>'; // Title for tooltip
            })
            ->addColumn('saved_by_user', function (MeetingMinute $minute) {
                return $minute->savedBy->name ?? 'N/A';
            })
            ->addColumn('action', function (MeetingMinute $minute) use ($request) {
                $all = null;
                if($request->filled('all')){
                    $all ='?all=yes';
            }
                $btn = '<a href="'.route('v1.meeting-minutes.detail', $minute->obfuscated_id).''.$all.'" class="btn btn-info btn-sm me-1">View</a>';
                // Add other actions like edit, delete if needed
                return $btn;
            })
            ->rawColumns(['attendees_list', 'action']) // Tell DataTables these columns contain HTML
            ->make(true);
    }

    /**
     * Display the specified meeting minute.
     *
     * @param  int  $id  The ID of the MeetingMinute.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            // Eager load attendees (and their users) and the user who saved it
            $decodedId = IdObfuscator::decode($id);
            $meetingMinute = MeetingMinute::with(['attendees.user', 'savedBy'])->findOrFail($decodedId);

            $breadcrumb = ['Dashboard', 'Meeting Minutes', 'Detail'];
            $title = 'Meeting Minute Details: ' . $meetingMinute->topic;

            return view('meeting_minutes.detail', compact('meetingMinute', 'breadcrumb', 'title'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // If meeting minute not found, redirect back with an error message
            return redirect()->route('v1.meeting-minutes.list')->with('errors', 'Meeting Minute not found.');
        } catch (\Exception $e) {
            // Log other unexpected errors
            \Log::error('Error displaying meeting minute: ' . $e->getMessage(), ['exception' => $e, 'meeting_id' => $id]);
            return redirect()->route('v1.meeting-minutes.list')->with('errors', 'An unexpected error occurred.');
        }
    }
}