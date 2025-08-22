<?php
namespace App\Services;

use App\Models\Product;
use App\Models\ReceivingOrder;
use App\Models\ReceivingOrderItem;
use App\Models\StockAdjustments;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ReceivingOrderService {

    public function list()
    {
        return view('inventories.receiving_orders.list')->with('title', 'Receiving Order')->with('breadcrumb', ['Home','Inventory Management', 'Receiving Order','List of Receiving Order']);
    }

    public function create()
    {
        $products = Product::all();
        return view('inventories.receiving_orders.form',compact('products'))->with('title', 'Create A New Receiving Order')->with('breadcrumb', ['Home','Inventory Management', 'Receiving Order','Create A New Receiving Order']);
    }

    public function getReceivingOrderData(Request $request)
    {
        $ordersQuery = ReceivingOrder::with([ 'createdBy']); 
        // adjust relationships based on your ReceivingOrder model

        if ($request->filled('month')) {
            $ordersQuery->whereMonth('received_date', $request->input('month'));
        }

        // Filter by year
        if ($request->filled('year')) {
            $ordersQuery->whereYear('received_date', $request->input('year'));
        }

        $orders = $ordersQuery->orderBy('created_at', 'ASC')->get();

        return DataTables::of($orders)
            ->addIndexColumn()
            ->addColumn('status', function (ReceivingOrder $order) {
                return ucfirst($order->status); // e.g., Pending, Completed
            })
            ->addColumn('receivedAt', function (ReceivingOrder $order) {
                return $order->received_date->format('d-M-Y H:i');
            })
            ->addColumn('purchaseAt', function (ReceivingOrder $order) {
                return $order->purchase_date->format('d-M-Y H:i');
            })
            ->addColumn('createdBy', function (ReceivingOrder $order) {
                return $order->createdBy ? $order->createdBy->name : 'System';
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group btn-group-vertical" role="group" aria-label="ReceivingOrder Actions">';

                // View button
                $btn .= '<a class="btn btn-info btn-sm view-order" href="'.route('v1.receiving-order.show',['id'=>$row->id]).'" 
                           >View</a>';

                if (auth()->user()->can('edit-receiving-order')) {
                    $btnx = '<a class="btn btn-primary btn-sm" 
                                href="' . route('v1.receiving-order.edit', ['id' => $row->id]) . '">Edit</a>';
                }

                if (auth()->user()->can('delete-receiving-order')) {
                    $btnx .= '<form method="POST" action="' . route('v1.receiving-order.delete', $row->id) . '" style="display:inline;">
                                ' . csrf_field() . method_field("DELETE") . '
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form>';
                }

                $btn .= '</div>';
                return $btn;
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Menyimpan pesanan penerimaan baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'po_number' => 'nullable|string|max:255',
            'supplier_name' => 'required',
            'received_date' => 'required|date',
            'purchase_date' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Gunakan transaksi database untuk memastikan semua data tersimpan atau tidak sama sekali
        DB::beginTransaction();

        try {
            $receivingOrder = ReceivingOrder::create([
                'po_number' => $validatedData['po_number'],
                'supplier_name' => $validatedData['supplier_name'],
                'received_date' => $validatedData['received_date'],
                'purchase_date' => $validatedData['purchase_date'],
                'remarks' => $validatedData['remarks'],
                'created_by' => auth()->user()->id
            ]);

            foreach ($validatedData['items'] as $item) {
                // Simpan setiap item ke tabel receiving_order_items
                ReceivingOrderItem::create([
                    'receiving_order_id' => $receivingOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);

                // Perbarui stok produk
                $product = Product::find($item['product_id']);
                $lastStock = $product->quantity;

                if ($product) {
                    $product->quantity += $item['quantity'];
                    $product->save();

                    StockAdjustments::create([
                        'product_id' => $product->id,
                        'adjust_type' => 1,
                        'quantity' => $item['quantity'],
                        'adjust_number' => $validatedData['po_number'],
                        'for_or_from' => $validatedData['supplier_name'],
                        'reason' => $validatedData['remarks'],
                        'user_id' => auth()->id(),
                        'previous_quantity'=> $lastStock,
                        'created_at'=>$validatedData['received_date'],
                        'updated_at'=>$validatedData['purchase_date']
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('v1.receiving-order.list')->with('success', 'Receiving Order saved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed created receiving order. ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail dari satu pesanan penerimaan.
     *
     * @param  \App\Models\ReceivingOrder  $receivingOrder
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $receivingOrder = ReceivingOrder::find($id);
        // Eager load relasi yang diperlukan untuk tampilan
        $receivingOrder->load('createdBy', 'items.product');

        return view('inventories.receiving_orders.show', compact('receivingOrder'))->with('title', 'Receiving Order')->with('breadcrumb', ['Home','Inventory Management', 'Receiving Order','Detail Receiving Order']);
    }

    

}