<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller; 
use App\Models\Client;
use App\Models\ClientRequest;
use App\Models\User;
use App\Models\ClientActivityLog;
use App\Models\ClientDownloadRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Notifications\UserNotification;
use Auth;
use App\Models\ClientRemark;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;
use App\Services\NotificationService;
use App\Services\CommonService;
use App\Services\ClientService;

class ClientController extends Controller
{
    protected $notificationService;
    protected $commonService;
    protected $clientService;

    public function __construct(
        NotificationService $notificationService,
        CommonService $commonService,
        ClientService $clientService)
    {
        $this->middleware('auth');
        $this->middleware('permission:view-client-database', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-client-database', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-client-database', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-client-database', ['only' => ['destroy']]);
        $this->notificationService = $notificationService;
        $this->commonService = $commonService;
        $this->clientService = $clientService;
    }

    public function index()
    {
              
        $salesPersonNotifications = $this->notificationService->getCountNotificationWithPermission('view-salesperson-assignment','v1/client-database/assignment-salesperson/list') ?? null;

        $recycleBinNotification = $this->notificationService->getCountNotificationWithPermission('view-client-recycle','v1/client-database/recycle-bin/list') ?? null;

        $clientDataNotifications = $this->notificationService->getCountNotification('v1/client-database/list');
        
        $requestNotifications = $this->notificationService->getCountNotification('v1/client-database/request-list');
        
        $PDFCSVDownloadRequestNotification = $this->notificationService->getCountNotification('v1/client-database/download-request/list');

        return view('client_database.index', compact('PDFCSVDownloadRequestNotification','requestNotifications', 'recycleBinNotification', 'clientDataNotifications', 'salesPersonNotifications'))->with('title', 'Client Database')->with('breadcrumb', ['Home', 'Client Database']);
    }

    //list
    public function list()
    {
        $this->notificationService->markClientListNotificationsAsRead('v1/client-database/list');

        $downloadFile = ClientDownloadRequest::where(['request_id' => auth()->user()->id])->get();

        return view('v2.client_database.list', [
            'industries' => $this->commonService->getIndustryCategories(),
            'countries' => $this->commonService->getSortedCountries(),
            'salesPersons' => $this->commonService->getUsers(),
            'downloadFile' => $downloadFile,
        ])->with('title', 'List of Client Database')->with('breadcrumb', ['Home', 'Client Database', 'List of Client Database']);

    }

    //get data for datatable
    public function getClientsData(Request $request)
    {
        return $this->clientService->getClientDataForDatatable($request);
        
    }

    public function assignmentSalesperson(Request $request)
    {
        return $this->clientService->assignSalesPerson($request);
    }

    public function bulkAssignmentSalesperson(Request $request)
    {
        return $this->clientService->bulkAssignmentSalesperson($request);
    }

    public function bulkDelete(Request $request)
    {
        return $this->clientService->bulkDelete($request);
    }

    public function bulkRequestToEdit(Request $request)
    {
        return $this->clientService->bulkRequestToEdit($request);
    }
}