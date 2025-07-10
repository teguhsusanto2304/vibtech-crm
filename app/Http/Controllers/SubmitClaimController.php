<?php

namespace App\Http\Controllers;

use App\Services\ProjectService;
use App\Services\NotificationService;
use App\Services\CommonService;
use App\Services\SubmitClaimService;
use Illuminate\Http\Request; 

class SubmitClaimController extends Controller
{
    protected $notificationService;
    protected $commonService;
    protected $submitClaimService;

    public function __construct(
        NotificationService $notificationService,
        CommonService $commonService,
        SubmitClaimService $submitClaimService)
    {
        $this->middleware('auth');

        $this->notificationService = $notificationService;
        $this->commonService = $commonService;
        $this->submitClaimService = $submitClaimService;
    }
    public function index()
    {
        return view('submit_claim.index')->with('title', 'Submit Claim')->with('breadcrumb', ['Home', 'Staff Task', 'Submit Claim']);
    }

    public function list()
    {
        return view('submit_claim.list')->with('title', 'List of Submit Claim')->with('breadcrumb', ['Home', 'Staff Task','List of Submit Claim']);
    }

    public function getSubmitClaimsData(Request $request)
    {
        return $this->submitClaimService->getSubmitClaimsData($request);
    }

    public function getSubmitClaimItemsData(Request $request,$submit_claim_id)
    {
        return $this->submitClaimService->getSubmitClaimItemsData($request,$submit_claim_id);
    }

    public function all()
    {
        return view('submit_claim.list')->with('title', 'List of All Submit Claim')->with('breadcrumb', ['Home', 'Staff Task','List of All Submit Claim']);
    }

    public function create(Request $request)
    {
        $dataClaim = null;
        if($request->query('id')){
            $dataClaim = $this->submitClaimService->getData($request->query('id'));
        }
        $claimTypes = $this->submitClaimService->getSubmitClaimType();
        return view('submit_claim.form',compact('claimTypes','dataClaim'))->with('title', 'Create A Submit Claim')->with('breadcrumb', ['Home', 'Staff Task','Create A Submit Claim']);
    }

    public function store(Request $request)
    {
        return  $this->submitClaimService->store($request);
    }

    public function detail($id)
    {
        return $this->submitClaimService->show($id);
    }

    public function getSubmitClaimItemDetails($id)
    {
        return $this->submitClaimService->getSubmitClaimItemDetails($id);
    }

    public function submitClaimDestroy($id)
    {
        return $this->submitClaimService->submitClaimDestroy($id);
    }

    public function submitClaimUpdateStatus(Request $request, $id)
    {
        return $this->submitClaimService->updateStatus($request,$id);
    }
}
