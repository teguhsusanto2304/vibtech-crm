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

    public function updateDescription(Request $request, $id)
    {
        return $this->submitClaimService->updateDescription($request,$id);
    }
    public function index()
    {
        return view('submit_claim.index')->with('title', 'Submit Claim')->with('breadcrumb', ['Home', 'Staff Task', 'Submit Claim']);
    }

    public function list()
    {
        return view('submit_claim.list')->with('title', 'Your Submitted Claims')->with('breadcrumb', ['Home', 'Staff Task','Your Submitted Claims']);
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
        return view('submit_claim.list')->with('title', 'All Submitted Claims')->with('breadcrumb', ['Home', 'Staff Task','All Submitted Claims']);
    }

    public function create(Request $request)
    {
        $dataClaim = null;
        if($request->query('id')){
            $dataClaim = $this->submitClaimService->getData($request->query('id'));
        }
        $claimTypes = $this->submitClaimService->getSubmitClaimType();
        return view('submit_claim.form',compact('claimTypes','dataClaim'))->with('title', 'Submit New Claim')->with('breadcrumb', ['Home', 'Staff Task','Submit New Claim']);
    }

    public function edit(Request $request,$id)
    {
        $dataClaim = null;
        if($request->query('id')){
            $dataClaim = $this->submitClaimService->getData($request->query('id'));
        }
        $dataClaimItem = $this->submitClaimService->getSubmitClaimItemData($id);
        $claimTypes = $this->submitClaimService->getSubmitClaimType();
        return view('submit_claim.edit',compact('claimTypes','dataClaim','dataClaimItem'))->with('title', 'Submit New Claim')->with('breadcrumb', ['Home', 'Staff Task','Submit New Claim']);
    }

    public function store(Request $request)
    {
        return  $this->submitClaimService->store($request);
    }

    public function update(Request $request, $id)
    {
        return  $this->submitClaimService->update($request,$id);
    }

    public function detail($id)
    {
        return $this->submitClaimService->show($id);
    }

    public function print($id)
    {
        return $this->submitClaimService->print($id);
    }

    public function getSubmitClaimItemDetails($id)
    {
        return $this->submitClaimService->getSubmitClaimItemDetails($id);
    }

    public function submitClaimDestroy($id)
    {
        return $this->submitClaimService->submitClaimDestroy($id);
    }

    public function destroyClaim($id) // Renamed method
    {
        return $this->submitClaimService->destroyClaim($id);
    }

    public function submitClaimUpdateStatus(Request $request, $id)
    {
        return $this->submitClaimService->updateStatus($request,$id);
    }

    public function handleApprovalAction(Request $request, $id)
    {
         return $this->submitClaimService->handleApprovalAction($request, $id);
    }

    public function handleRejectedAction(Request $request, $id)
    {
        return $this->submitClaimService->handleRejectedAction($request, $id);
    }

    public function exchange(Request $request)
    {
        return $this->submitClaimService->exchange($request);
    }

    public function getRates(Request $request)
    {
        return $this->submitClaimService->getRates($request);
    }

    public function adjustClaimItem(Request $request)
    {
        return $this->submitClaimService->adjustClaimItem($request);
    }
}
