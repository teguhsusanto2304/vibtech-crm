<?php

namespace App\Http\Controllers;

use App\Models\SalesForecast;
use Illuminate\Http\Request;
use App\Services\CommonService;
use App\Services\SalesForecastService;

class SalesForecastController extends Controller
{
    protected $notificationService;
    protected $commonService;
    protected $salesForecastService;

    public function __construct(
        CommonService $commonService,
        SalesForecastService $salesForecastService)
    {
        $this->middleware('auth');
        $this->commonService = $commonService;
        $this->salesForecastService = $salesForecastService;
    }
    public function index()
    {
        return view('sales.sales_forecast.index')->with('title', 'Sales Forecast')->with('breadcrumb', ['Home', 'Sales','Sales Forecast']);
    }

    public function create()
    {
        $individuallies = $this->commonService->getIndividuallies();
        return view('sales.sales_forecast.form',compact('individuallies'))->with('title', 'Sales Forecast')->with('breadcrumb', ['Home', 'Sales','Sales Forecast','Create']);
    }

    public function store(Request $request)
    {
        return $this->salesForecastService->store($request);   
    }

    public function showForecastGrid(Request $request)
    {
        return $this->salesForecastService->showForecastGrid($request);   
    }

    public function save(Request $request)
    {
        return $this->salesForecastService->save($request);   
    }

    public function storeVariable(Request $request)
    {
        return $this->salesForecastService->storeVariable($request);  
    }

    public function addCompany(Request $request)
    {
         return $this->salesForecastService->addCompany($request);  
    }

    public function review(Request $request)
    {
        return $this->salesForecastService->salesForecastReviews($request);
    }

    public function getSalesForecastData(Request $request)
    {
        return $this->salesForecastService->getSalesForecastData($request);
    }

    public function reviewDetail($id)
    {
        return $this->salesForecastService->reviewDetail($id);
    }

}
