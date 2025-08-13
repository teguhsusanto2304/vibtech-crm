<?php
namespace App\Services;

use App\Models\Product;
use App\Models\StockAdjustments;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductService {
    public function getProductsData(Request $request)
    {
        $productsQuery = Product::with(['productCategory']); // Assuming projectMembers is the correct relationship name

        
        $products = $productsQuery->orderBy('created_at', 'ASC')->get();


        return DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('category_name', function (Product $product) {
                return $product->productCategory->name;
            })
            ->addColumn('createdAt', function (Product $product) {
                return $product->created_at->format('d-M-Y H:i');
            })
            ->addColumn('updatedAt', function (Product $product) {
                return $product->updated_at->format('d-M-Y H:i');
            })
            ->addColumn('action', function ($row) {
                // Start with the vertical button group container
                $btn = '<div class="btn-group btn-group-vertical" role="group" aria-label="Project Actions">';
                $btn .= '<div class="btn-group btn-group-vertical" role="group" aria-label="Project Actions">';

                // View button
                $btn .= '<a class="btn btn-info btn-sm" href="' . route('v1.project-management.management-detail', ['project' => $row->obfuscated_id]) . '">View</a>';
                $btn .= '<a class="btn btn-primary btn-sm" href="' . route('v1.project-management.management-detail', ['project' => $row->obfuscated_id]) . '">Edit</a>';
                $btn .= '</div>';
                $btn .= '<a class="btn btn-warning btn-sm" href="' . route('v1.project-management.management-detail', ['project' => $row->obfuscated_id]) . '">Stock Adjustment</a>';

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
            'product_category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:0',
            'created_by' => 'required|string|max:255',
        ]);
        Product::create($request->all());
        return redirect()->route('inventory.index')->with('success', 'Produk berhasil ditambahkan!');
    }
    
    public function edit(Product $product)
    {
        $categories = ProductCategory::all();
        return view('inventory.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku_no' => 'required|string|unique:products,sku_no,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:0',
            'created_by' => 'required|string|max:255',
        ]);
        $product->update($request->all());
        return redirect()->route('inventory.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function adjustStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'adjust_type' => ['required', Rule::in(['increase', 'decrease'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'adjust_number' => ['nullable', 'string', 'max:255'],
            'for_or_from' => ['nullable', 'string', 'max:255'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::findOrFail($validated['product_id']);

            if ($validated['adjust_type'] === 'increase') {
                $product->stock += $validated['quantity'];
            } else {
                $product->stock = max(0, $product->stock - $validated['quantity']);
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
            ]);
        });

        return back()->with('success', 'Stock adjustment saved successfully.');
    }

}