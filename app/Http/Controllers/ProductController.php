<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Services\CommonService;

class ProductController extends Controller
{
    protected $commonService;
    protected $productService;

    public function __construct(
        CommonService $commonService,
        ProductService $productService)
    {
        $this->middleware('auth');
        $this->commonService = $commonService;
        $this->productService = $productService;
    }

    public function index()
    {
        return view('inventories.index')->with('title', 'Inventory Management')->with('breadcrumb', ['Home', 'Inventory Management']);
    }

    public function list()
    {
        return view('inventories.list')->with('title', 'Inventory Management')->with('breadcrumb', ['Home', 'Inventory Management','List of Inventory']);
    }

    public function create()
    {
        $productCategories = $this->commonService->getProductCategories();
        return view('inventories.form',compact('productCategories'))->with('title', 'Create An New Inventory')->with('breadcrumb', ['Home', 'Inventory Management','Create An New Inventory']);
    }

    public function getProductsData(Request $request)
    {
        return $this->productService->getProductsData($request);
    }

    public function store(Request $request)
    {
        return $this->productService->store($request);
    }

    public function adjustStock(Request $request)
    {
        return $this->productService->adjustStock($request);
    }
}
