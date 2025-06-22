<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProjectStageTaskService;
use App\Services\NotificationService;
use App\Services\CommonService;
use Illuminate\Http\Request; 

class ProjectStageTaskController extends Controller
{
    protected $notificationService;
    protected $commonService;
    protected $projectStageTaskService;

    public function __construct(
        NotificationService $notificationService,
        CommonService $commonService,
        ProjectStageTaskService $projectStageTaskService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
        $this->commonService = $commonService;
        $this->projectStageTaskService = $projectStageTaskService;
    }

    public function store(Request $request, $project_id, $stage_id)
    {
        return $this->projectStageTaskService->store($request, $project_id, $stage_id);
    }

    public function show($task_id)
    {
        return $this->projectStageTaskService->show($task_id);
    }

    public function updateStatus(Request $request, $task_id)
    {
        return $this->projectStageTaskService->updateStatus($request,$task_id);
    }

    public function addLog(Request $request, $task_id)
    {
        return $this->projectStageTaskService->addLog($request,$task_id);
    }

}
