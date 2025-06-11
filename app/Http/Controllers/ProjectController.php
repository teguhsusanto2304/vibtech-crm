<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use App\Services\NotificationService;
use App\Services\CommonService;
use Illuminate\Http\Request; 

class ProjectController extends Controller
{
    protected $notificationService;
    protected $commonService;
    protected $projectService;

    public function __construct(
        NotificationService $notificationService,
        CommonService $commonService,
        ProjectService $projectService)
    {
        $this->middleware('auth');
        $this->middleware('permission:view-project-management', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-project-management', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-project-management', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-project-management', ['only' => ['destroy']]);
        $this->notificationService = $notificationService;
        $this->commonService = $commonService;
        $this->projectService = $projectService;
    }

    public function index()
    {
        return view('projects.index')->with('title', 'Project Management')->with('breadcrumb', ['Home', 'Project Management']);
    }

    public function list()
    {
        return view('projects.list')->with('title', 'List of Projects')->with('breadcrumb', ['Home', 'Project Management','List of Projects']);
    }

    public function create()
    {
        $users = $this->commonService->getUsers();
        return view('projects.form',compact('users'))->with('title', 'Create A New Project')->with('breadcrumb', ['Home', 'Project Management','Create A New Project']);
    }

    public function store(Request $request)
    {
        return $this->projectService->store($request);
    }

    public function detail($id)
    {
        $project = $this->projectService->getProject($id);
        $kanbanStages = $this->commonService->getKanbanStages();
        return view('projects.detail',compact('project','kanbanStages'))->with('title', 'Project Detail')->with('breadcrumb', ['Home', 'Project Management','Project Detail']);
    }

    public function getProjectsData(Request $request)
    {
        return $this->projectService->getProjectsData($request);
    }
}