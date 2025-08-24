<?php
namespace App\Services;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockAdjustments;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductService {
    /**
     * Dapatkan data riwayat penyesuaian stok untuk DataTables.
     */
    public function getProductHistory(Request $request,$productId)
    {
        // Parameter dari DataTables
        // Parameter dari DataTables
        $start = $request->input('start');
        $length = $request->input('length');
        $searchValue = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir');
        $draw = $request->input('draw');

        // Mapping kolom ke nama kolom database
        $columns = [
            'created_at',
            null, // Kolom history tidak dapat diurutkan
            'user_id'
        ];
        $orderColumn = $columns[$orderColumnIndex];

        // Query dasar dengan relasi
        $query = StockAdjustments::where('product_id',$productId)->with('user');
        $totalRecords = $query->count();

        // Filter pencarian
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('adjustment_type', 'like', "%{$searchValue}%")
                  ->orWhere('notes', 'like', "%{$searchValue}%")
                  ->orWhereHas('user', function ($q) use ($searchValue) {
                      $q->where('name', 'like', "%{$searchValue}%");
                  });
            });
        }

        // Total data setelah filter
        $filteredRecords = $query->count();

        // Pengurutan
        if (!is_null($orderColumn)) {
            $query->orderBy($orderColumn, $orderDir);
        }
        
        // Pagination
        $query->offset($start)->limit($length);

        $stockAdjustments = $query->orderBy('created_at','DESC')->get();

        // Format data untuk DataTables
        $formattedData = $stockAdjustments->map(function ($item) {
            $history = '';
            
            // Format untuk "Increase Stock"
            if ($item->adjustment_type === 1) {
                $history = "Adjustment Type: Increase Stock<br>"
                         . "Previous Product Total: {$item->previous_quantity}<br>"
                         . "Adjustment: +{$item->quantity_adjusted}<br>"
                         . "New Product Total: {$item->new_quantity}<br>"
                         . "PO Number (Vibtech): {$item->po_number}<br>"
                         . "From: {$item->source}<br>"
                         . "Product Purchased Date: " . ($item->purchase_date ? $item->purchase_date->format('d-m-Y') : '-') . "<br>"
                         . "Product Received Date: ".($item->received_date ? $item->received_date->format('d-m-Y') : '-')."<br>"
                         . "Remarks: {$item->notes}";

            // Format untuk "Decrease Stock"
            } elseif ($item->adjustment_type === 2) {
                $history = "Adjustment Type: Decrease Stock<br>"
                         . "Previous Product Total: {$item->previous_quantity}<br>"
                         . "Adjustment: -{$item->quantity_adjusted}<br>"
                         . "New Product Total: {$item->new_quantity}<br>"
                         . "PO Number (Client): {$item->po_number}<br>"
                         . "Product Draw Out Date: ".($item->draw_out_date ? $item->draw_out_date->format('d-m-Y') : '-')."<br>"
                         . "Remarks: {$item->notes}";
            }

            // Contoh tambahan untuk 'Product created'
            if ($item->adjustment_type === 0) {
                 $history = "Product created via Create New Inventory<br>"
                          . "Created Product Total: {$item->new_total}";
             }
            
            return [
                'date' => $item->created_at->format('d-m-Y H:i:s'),
                'history' => $history,
                'staff' => $item->user->name ?? 'N/A',
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($filteredRecords),
            'data' => $formattedData,
        ]);
    }

    public function getProductsData(Request $request)
    {
        $productsQuery = Product::with(['productCategory','createdBy','latestStockAdjustment']); // Assuming projectMembers is the correct relationship name
        if ($request->filled('category_id')) {
            $productsQuery->where('product_category_id', $request->input('category_id'));
        }
        
        $products = $productsQuery->orderBy('created_at', 'ASC')->get();


        return DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('product_image', function ($product) {
                // Cek apakah produk memiliki gambar
                if ($product->image) {
                    // Gunakan asset() untuk mendapatkan URL publik gambar
                    // Sesuaikan 'storage/' jika Anda menyimpan di path lain
                    return '<img src="' . asset('storage/' . $product->image) . '" alt="Product Image" style="width: 50px; height: 50px; border-radius: 8px;">';
                }
                // Jika tidak ada gambar, tampilkan placeholder atau teks
                return 'No Image';
            })
            ->addColumn('category_name', function (Product $product) {
                return $product->productCategory->name;
            })
            ->addColumn('createdAt', function (Product $product) {
                return $product->created_at->format('d-M-Y H:i');
            })
            ->addColumn('updatedAt', function (Product $product) {
                if ($product->latestStockAdjustment) {
                    return $product->latestStockAdjustment->created_at->format('d-M-Y H:i');
                }
                return 'N/A'; // Atau string lain yang sesuai jika data null
            })
            ->addColumn('updatedBy', function (Product $product) {
                if ($product->latestStockAdjustment) {
                    return $product->latestStockAdjustment->user->name;
                }
                return 'N/A'; // Atau string lain yang sesuai jika data null
            })
            ->addColumn('createdBy', function (Product $product) {
                return $product->createdBy->name;
            })
            ->addColumn('action', function ($row) {
                // Start with the vertical button group container
                $btn = '<div class="btn-group btn-group-vertical" role="group" aria-label="Project Actions">';
                $btn .= '<div class="btn-group btn-group-vertical" role="group" aria-label="Project Actions">';

                // View button
                $btn .= '<a class="btn btn-info btn-sm view-product" href="#" data-bs-toggle="modal" data-bs-target="#productDetailModal" data-id="' . $row->id . '">View</a>';
                if (auth()->user()->can('edit-inventory-management')) {
                    $btn .= '<a class="btn btn-primary btn-sm" href="' . route('v1.inventory-management.edit', ['id'=>$row->id]) . '">Edit</a>';
                    $btn .= '</div>';
                    $btn .= '<a class="btn btn-warning btn-sm text-white" data-product-id ="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#adjustStockModal">Stock Adjustment</a>';
                }
                $btn .= '</div>';

                return $btn;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku_no' => 'required|string|unique:products,sku_no',
            'product_category_id' => 'required|exists:product_categories,id',
            'quantity' => 'required|integer|min:0',
            'path_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($request->hasFile('path_image')) {
            // Simpan gambar ke storage dan dapatkan path-nya
            $imagePath = $request->file('path_image')->store('inventories', 'public');
            // Tambahkan path gambar ke data yang divalidasi
            $request['image'] = $imagePath;
        }
        $request['created_by'] = auth()->user()->id;
        
        $product = Product::create($request->all());
        $stockAdjustment = new StockAdjustments([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'adjustment_type' => 0,
                    'quantity_adjusted' => $product->quantity,
                    'previous_quantity' => 0,
                    'new_quantity' => $product->quantity,
                    'po_number' => 'N/A',
                    'source' => 'N/A',
                    'purchase_date' => date('Y-m-d'),
                    'received_date' => date('Y-m-d'),
                    'notes' => 'Product created via Create New Inventory',
                ]);
                $stockAdjustment->save();

        return redirect()->route('v1.inventory-management.list')->with('success', 'Inventory has been succesfully stored!');
    }

    public function categoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $category = ProductCategory::create($request->all());
        return response()->json([
            'message' => 'Product category saved successfully.',
            'id'=>$category->id,
            'name'=>$category->name
        ], 200);
    }

    public function categoryUpdate(Request $request,$id)
    {
        $category = ProductCategory::find($id);
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $category->name = $request->name;
        $category->save();
        return response()->json([
            'message' => 'Product category saved successfully.',
            'id'=>$category->id,
            'name'=>$category->name
        ], 200);
    }

    public function categoryDelete($id)
    {
        $category = ProductCategory::find($id);
        
        $category->data_status = 0;
        $category->save();
        return response()->json([
            'message' => 'Product category deleted successfully.',
            'id'=>$category->id,
            'name'=>$category->name
        ], 200);
    }
    
    public function getProductData($id)
    {
        return Product::find($id);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'sku_no' => 'required|string|unique:products,sku_no,' . $product->id,
            'product_category_id' => 'required|exists:product_categories,id',
            'path_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
         if ($request->hasFile('path_image')) {
            // Simpan gambar ke storage dan dapatkan path-nya
            $imagePath = $request->file('path_image')->store('inventories', 'public');
            // Tambahkan path gambar ke data yang divalidasi
            $request['image'] = $imagePath;
        }
        $product->update($request->all());
        return redirect()->route('v1.inventory-management.list')->with('success', 'Inventory has been updated!');
    }

    /**
     * Simpan penyesuaian stok.
     */
    public function adjustStock(Request $request)
    {
        // Validasi data input
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'adjust_type' => 'required|in:1,2', // 1 untuk Increase, 2 untuk Decrease
            'quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string|max:255',
        ]);

        $productId = $validatedData['product_id'];
        $product = Product::findOrFail($productId);
        $adjustmentType = $validatedData['adjust_type'];
        $quantity = $validatedData['quantity'];
        
        // Mulai transaksi database untuk memastikan konsistensi
        DB::beginTransaction();

        try {
            if ($adjustmentType == 1) { // Increase Stock
                // Lakukan validasi tambahan untuk Increase Stock
                $request->validate([
                    'po_number_increase' => 'nullable|string|max:255',
                    'source' => 'nullable|string|max:255',
                    'purchase_date' => 'nullable|date',
                    'receive_date' => 'nullable|date',
                ]);

                // Update kuantitas produk
                $product->quantity += $quantity;
                $product->save();

                // Simpan data ke tabel stock_adjustments
                $stockAdjustment = new StockAdjustments([
                    'product_id' => $productId,
                    'user_id' => auth()->id(),
                    'adjustment_type' => 1,
                    'quantity_adjusted' => $quantity,
                    'previous_quantity' => $product->quantity - $quantity,
                    'new_quantity' => $product->quantity,
                    'po_number' => $request->input('po_number_increase'),
                    'source' => $request->input('source'),
                    'purchase_date' => $request->input('purchase_date'),
                    'received_date' => $request->input('receive_date'),
                    'notes' => $request->input('remarks'),
                ]);
                $stockAdjustment->save();

            } elseif ($adjustmentType == 2) { // Decrease Stock
                // Lakukan validasi tambahan untuk Decrease Stock
                $request->validate([
                    'po_number_decrease' => 'nullable|string|max:255',
                    'draw_out_date' => 'nullable|date',
                ]);

                // Pastikan stok tidak menjadi negatif
                if ($product->quantity < $quantity) {
                    return back()->with('error', 'Quantity to decrease is more than current stock.');
                }

                // Update kuantitas produk
                $product->quantity -= $quantity;
                $product->save();

                // Simpan data ke tabel stock_adjustments
                $stockAdjustment = new StockAdjustments([
                    'product_id' => $productId,
                    'user_id' => auth()->id(),
                    'adjustment_type' => 2,
                    'quantity_adjusted' => -$quantity, // Simpan sebagai nilai negatif
                    'previous_quantity' => $product->quantity + $quantity,
                    'new_quantity' => $product->quantity,
                    'po_number' => $request->input('po_number_decrease'),
                    'draw_out_date' => $request->input('draw_out_date'),
                    'notes' => $request->input('remarks'),
                ]);
                $stockAdjustment->save();
            }

            DB::commit(); // Simpan perubahan
           return response()->json([
            'success'=>true,
            'message' => 'Stock adjustment saved successfully.'
        ], 201);

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan jika terjadi kesalahan
            //return back()->with('error', 'Failed to adjust stock. ' . $e->getMessage());
            return response()->json([
            'message' => 'Failed to adjust stock. ' . $e->getMessage()
        ], 500);
        }
    }

    public function adjustStockOld(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'adjust_type' => ['required'],
            'quantity' => ['required', 'integer', 'min:1'],
            'adjust_number' => ['nullable', 'string', 'max:255'],
            'for_or_from' => ['nullable', 'string', 'max:255'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::findOrFail($validated['product_id']);
            $lastStock = $product->quantity;

            if ((int) $validated['adjust_type'] === 1) {
                $product->quantity += (int) $validated['quantity'];
            } else {
                $product->quantity = max(0, $product->quantity - (int) $validated['quantity']);
            }
            $product->save();

            StockAdjustments::create([
                'product_id' => $product->id,
                'adjust_type' => $validated['adjust_type'],
                'quantity' => $validated['quantity'],
                'adjust_number' => $validated['adjust_number'],
                'for_or_from' => $validated['for_or_from'],
                'reason' => $validated['reason'],
                'user_id' => auth()->id(),
                'previous_quantity'=> $lastStock
            ]);
        });

        return response()->json([
            'message' => 'Stock adjustment saved successfully.'
        ], 200);
    }

    public function show($product)
    {
        // Muat relasi kategori
        $product = Product::find($product);
        $product->load('productCategory');
        // Kembalikan produk sebagai respons JSON
        return response()->json($product);
    }

    /**
     * Mengambil histori penyesuaian stok untuk sebuah produk.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStockHistory($productId)
    {
        $product = Product::find($productId);
        $history = $product->stockAdjustments()
                           ->with('user') // Memuat relasi createdBy
                           ->latest() // Mengurutkan dari yang terbaru
                           ->get();

        return response()->json([
            'data' => $history
        ]);
    }

}