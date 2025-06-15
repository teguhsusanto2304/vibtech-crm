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
}
