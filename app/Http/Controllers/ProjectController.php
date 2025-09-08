<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use App\Services\NotificationService;
use App\Services\CommonService;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Session;

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

    public function projectList()
    {
        return view('projects.project.list')->with('title', 'List of Projects')->with('breadcrumb', ['Home', 'Project Management','List of Projects']);
    }

    public function all()
    {
        return view('projects.management.list')->with('title', 'Vibtech Projects')->with('breadcrumb', ['Home', 'Project Management','Vibtech Projects']);
    }

    public function create()
    {
        $users = $this->commonService->getUsers();
        $users = $users->where('id', '!=', auth()->user()->id);
        return view('projects.form',compact('users'))->with('title', 'Create A New Project')->with('breadcrumb', ['Home', 'Project Management','Create A New Project']);
    }

    public function createProject()
    {
        $users = $this->commonService->getUsers();
        $users = $users->where('id', '!=', auth()->user()->id);
        return view('projects.project.form',compact('users'))->with('title', 'Create A New Project')->with('breadcrumb', ['Home', 'Project Management','Create A New Project']);
    }

    public function store(Request $request)
    {
        return $this->projectService->store($request);
    }

    public function storeProject(Request $request)
    {
        return $this->projectService->storeProject($request);
    }

    public function edit($id)
    {
        $users = $this->commonService->getUsers();
        $users = $users->where('id', '!=', auth()->user()->id);
        $project = $this->projectService->getProject($id);
        return view('projects.edit',compact('project','users'))->with('title', 'Edit Project')->with('breadcrumb', ['Home', 'Project Management','Edit Project']);
    }

    public function update(Request $request,$id)
    {
        return $this->projectService->update($request,$id);
    }

    public function destroy($id)
    {
        return $this->projectService->destroy($id);
    }

    public function detail($id)
    {
        $project = $this->projectService->getProject($id);
        if (!$project) {
                return redirect()->route('v1.project-management.list')->with('errors', 'Data not finded');
        }

        $selectedPhaseId = Session::get('selected_project_phase_id_' . $project->id);
        $phases = $project->phases()->get();
        if (is_null($selectedPhaseId) && $phases->isNotEmpty()) {
            
            $firstPhase = $phases->first(); // Ambil phase pertama
            $selectedPhaseId = $firstPhase->id; // Gunakan ID phase pertama sebagai default

            Session::put('selected_project_phase_id_' . $project->id, $selectedPhaseId);
            $selectedPhaseId = Session::get('selected_project_phase_id_' . $project->id);
        }

        if (Session::get('selected_project_phase_id_' . $project->id) != $selectedPhaseId) {
            Session::forget('selected_project_phase_id_' . $project->id);
            Session::put('selected_project_phase_id_' . $project->id, $selectedPhaseId);
            $selectedPhaseId = Session::get('selected_project_phase_id_' . $project->id);
        }
        
        $users = $this->commonService->getUsers();
        $users = $users->where('id', '!=', auth()->user()->id);
        $kanbanStages = $this->commonService->getKanbanStages();
        $creators = $this->projectService->getFileCreator($id);
        return view('projects.detail',compact('project','kanbanStages','users','creators','selectedPhaseId'))->with('title', 'Project Detail')->with('breadcrumb', ['Home', 'Project Management','Project Detail']);
    }

    public function detailProject($id)
    {
        $project = $this->projectService->getProject($id);
        if (!$project) {
                return redirect()->route('v1.project-management.list')->with('errors', 'Data not finded');
        }

        $selectedPhaseId = Session::get('selected_project_phase_id_' . $project->id);
        $phases = $project->phases()->get();
        if (is_null($selectedPhaseId) && $phases->isNotEmpty()) {
            
            $selectedPhaseId = $project->current_phase;

            Session::put('selected_project_phase_id_' . $project->id, $selectedPhaseId);
            $selectedPhaseId = Session::get('selected_project_phase_id_' . $project->id);
        }

        if (Session::get('selected_project_phase_id_' . $project->id) != $selectedPhaseId) {
            Session::forget('selected_project_phase_id_' . $project->id);
            Session::put('selected_project_phase_id_' . $project->id, $selectedPhaseId);
            $selectedPhaseId = Session::get('selected_project_phase_id_' . $project->id);
        }
        
        $users = $this->commonService->getUsers();
        $users = $users->where('id', '!=', auth()->user()->id);
        $kanbanStages = $this->commonService->getKanbanStages();
        $creators = $this->projectService->getFileCreator($id);
        $ganttData = $this->projectService->ganttData($project->id,'month',$selectedPhaseId);
        return view('projects.project.detail',compact('ganttData','project','kanbanStages','users','creators','selectedPhaseId'))->with('title', 'Project Detail')->with('breadcrumb', ['Home', 'Project Management','Project Detail']);
    }

    public function phase($project_id,$id)
    {
        $project = $this->projectService->getProject($project_id);
        $selectedPhaseId = Session::get('selected_project_phase_id_' . $project->id);

        if ($selectedPhaseId != $id) {
            Session::forget('selected_project_phase_id_' . $project->id);
            Session::put('selected_project_phase_id_' . $project->id, $id);
            Session::get('selected_project_phase_id_' . $project->id);
        }

        return redirect()->route('v1.project-management.detail', ['project' => $project_id])
                         ->with('success', 'You has choosen phases filter.');

    }

    public function phaseProject($project_id,$id)
    {
        $project = $this->projectService->getProject($project_id);
        $selectedPhaseId = Session::get('selected_project_phase_id_' . $project->id);

        if ($selectedPhaseId != $id) {
            Session::forget('selected_project_phase_id_' . $project->id);
            Session::put('selected_project_phase_id_' . $project->id, $id);
            Session::get('selected_project_phase_id_' . $project->id);
        }

        return redirect()->route('v1.projects.detail', ['project' => $project_id])
                         ->with('success', 'You has choosen phases filter.');

    }

    

    public function managementDetail($id)
    {
        $project = $this->projectService->getProject($id);
        if (!$project) {
                return redirect()->route('v1.project-management.all')->with('errors', 'Data not finded');
        }
        
        $users = $this->commonService->getUsers();
        $users = $users->where('id', '!=', auth()->user()->id);
        $kanbanStages = $this->commonService->getKanbanStages();
        return view('projects.management.detail',compact('project','kanbanStages','users'))->with('title', 'Project Detail')->with('breadcrumb', ['Home', 'Project Management','Project Detail']);
    }

    public function addMember(Request $request, $id)
    {
        return $this->projectService->addMember($request,$id);
    }

    public function removeMember($project_id, $user_id)
    {
        return $this->projectService->removeMember($project_id, $user_id);
    }

    public function markComplete(Request $request, $project_id, $project_stage_id)
    {
        return $this->projectService->markComplete($request, $project_id, $project_stage_id);
    }

    public function markProjectComplete(Request $request, $project_id)
    {
        return $this->projectService->markProjectComplete($request, $project_id);
    }

    public function completePhase(Request $request, $project_id, $phase_id)
    {
        return $this->projectService->completePhase($request, $project_id, $phase_id);
    }

    public function getProjectsData(Request $request)
    {
        return $this->projectService->getProjectsData($request);
    }

    public function getAllProjectsData(Request $request)
    {
        return $this->projectService->getAllProjectsData($request);
    }

    public function getAssignableUsers($id)
    {
        return $this->projectService->getAssignableUsers($id);
    }

    public function fileDestroy(string $projectFileId)
    {
        return $this->projectService->fileDestroy($projectFileId);
    }

    public function getStageBulletinsData($projectStageId,$projectId)
    {
        return $this->projectService->getStageBulletinsData($projectStageId,$projectId);
    }

    public function storeBulletin(Request $request, $projectStageId,$projectId)
    {
        return $this->projectService->storeBulletin($request,$projectStageId,$projectId);
    }

    public function monthlyStatusChartData()
    {
        return $this->projectService->monthlyStatusChartData();
    }

    public function getProjectFileData(Request $request)
    {
        return $this->projectService->getProjectFileData($request);
    }

    public function getPhaseDetailsForModal($projectId, $phaseId, Request $request)
    {
        return $this->projectService->getPhaseDetailsForModal($projectId, $phaseId, $request);
    }

     public function getStageDetailsForModal($projectId, $stageId, Request $request)
    {
        return $this->projectService->getStageDetailsForModal($projectId, $stageId, $request);
    }

    public function updateProjectPhase(Request $request, $projectId, $phaseId)
    {
        return $this->projectService->updateProjectPhase($request, $projectId, $phaseId);
    }

    public function showKanban($projectId) 
    {
        return $this->projectService->showKanban($projectId);
    }

    public function showGantt($projectId)
    {
        return $this->projectService->showGantt($projectId);
    }

    public function ganttViewBootstrap($projectId)
    {
        return $this->projectService->ganttViewBootstrap($projectId);
    }

    public function showGanttDaily($projectId)
    {
        return $this->projectService->showGantt($projectId,'daily');
    }

    public function storeProjectTask(Request $request)
    {
        return $this->projectService->storeProjectTask($request);
    }

    public function moveProjectTask(Request $request, $taskId)
    {
        return $this->projectService->moveProjectTask($request, $taskId);
    }

    public function setDefaultPhase($project_id, $id)
    {
        return $this->projectService->setDefaultPhase($project_id, $id);
    }

    public function storeProjectSTage(Request $request)
    {
        return $this->projectService->storeProjectSTage($request);
    }

    public function updateProjectStage(Request $request, $id)
    {
         return $this->projectService->updateProjectStage($request, $id);
    }

    public function getProjectTaskData($projectId)
    {
        return $this->projectService->getProjectTaskData($projectId);
    }
}