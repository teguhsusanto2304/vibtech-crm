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
        StockAdjustments::create([
                'product_id' => $product->id,
                'adjust_type' => 3,
                'quantity' => $product->quantity,
                'adjust_number' => 'N/A',
                'for_or_from' => 'N/A',
                'reason' => 'Product created via Create New Inventory ',
                'user_id' => auth()->id(),
                'previous_quantity'=> 0
            ]);
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

    public function adjustStock(Request $request)
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