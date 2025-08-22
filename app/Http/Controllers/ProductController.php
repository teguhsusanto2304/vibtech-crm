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

    public function edit($id)
    {
        $product = $this->productService->getProductData($id);
        $productCategories = $this->commonService->getProductCategories();
        return view('inventories.edit',compact('productCategories','product'))->with('title', 'Edit Inventory')->with('breadcrumb', ['Home', 'Inventory Management','Edit Inventory']);
    }

    public function getProductsData(Request $request)
    {
        return $this->productService->getProductsData($request);
    }

    public function store(Request $request)
    {
        return $this->productService->store($request);
    }

    public function update(Request $request,$id)
    {
        return $this->productService->update($request, $id);
    }

    public function adjustStock(Request $request)
    {
        return $this->productService->adjustStock($request);
    }

    public function show($product)
    {
        return $this->productService->show($product);
    }

    public function categoryStore(Request $request)
    {
         return $this->productService->categoryStore($request);
    }

    public function categoryUpdate(Request $request,$id)
    {
         return $this->productService->categoryUpdate($request,$id);
    }

    public function categoryDelete($id)
    {
         return $this->productService->categoryDelete($id);
    }

    public function getStockHistory($productId)
    {
        return $this->productService->getStockHistory($productId);
    }
}
