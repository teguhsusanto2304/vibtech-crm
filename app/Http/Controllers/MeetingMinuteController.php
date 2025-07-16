<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Services\CommonService;
use App\Services\MeetingMinuteService;
use App\Models\MeetingMinute;
use Illuminate\Support\Facades\Response;
use App\Models\MeetingAttendee;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class MeetingMinuteController extends Controller
{
    protected $notificationService;
    protected $commonService;
    protected $meetingMinuteService;

    public function __construct(
        NotificationService $notificationService,
        CommonService $commonService,
        MeetingMinuteService $meetingMinuteService)
    {
        $this->middleware('auth');

        $this->notificationService = $notificationService;
        $this->commonService = $commonService;
        $this->meetingMinuteService = $meetingMinuteService;
    }
    
    public function index()
    {
        return view('meeting_minutes.index')->with('title', 'Meeting Minutes')->with('breadcrumb', ['Home', 'Meeting Minutes']);
    }

    public function list()
    {
        return view('meeting_minutes.list')->with('title', 'Your Meeting Minutes')->with('breadcrumb', ['Home', 'Your Meeting Minutes']);
    }

    public function all()
    {
        return view('meeting_minutes.all')->with('title', 'Your Meeting Minutes')->with('breadcrumb', ['Home', 'Your Meeting Minutes']);
    }

    public function create()
    {
        return $this->meetingMinuteService->create();
    }

    public function store(Request $request)
    {
        return $this->meetingMinuteService->store($request);
    }

    public function getMeetingMinutesData(Request $request)
    {
        return $this->meetingMinuteService->getMeetingMinutesData($request);
    }

    public function show($id)
    {
        return $this->meetingMinuteService->show($id);
    }

}
