<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReceivingOrder;
use App\Models\ReceivingOrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

use App\Services\ReceivingOrderService;

class ReceivingOrderController extends Controller
{
    protected $receivingOrder;

    public function __construct(
        ReceivingOrderService $receivingOrder
    ){
        $this->receivingOrder = $receivingOrder;
    }
    /**
     * Menampilkan daftar semua pesanan penerimaan.
     */
    public function list()
    {
        return $this->receivingOrder->list();
    }

    /**
     * Menampilkan formulir untuk membuat pesanan penerimaan baru.
     */
    public function create()
    {
        return $this->receivingOrder->create();
    }

    public function getReceivingOrderData(Request $request)
    {
        return $this->receivingOrder->getReceivingOrderData($request);
    }

    public function store(Request $request)
    {
        return $this->receivingOrder->store($request);
    }

    public function show($id)
    {
        return $this->receivingOrder->show($id);
    }



    
}
