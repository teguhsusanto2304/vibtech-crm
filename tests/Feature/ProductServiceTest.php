<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\ProductService;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\StockAdjustments;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * Kelas tes ini menguji fungsionalitas dari ProductService.
 * Menggunakan RefreshDatabase untuk memastikan database bersih sebelum setiap tes.
 */
class ProductServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $productService;
    protected $user;

    // Metode ini berjalan sebelum setiap metode tes.
    protected function setUp(): void
    {
        parent::setUp();
        // Inisialisasi service yang akan diuji.
        $this->productService = new ProductService();
        
        // Buat user dummy yang akan digunakan untuk otentikasi.
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }
    
    /** @test */
    public function it_can_store_a_new_product_and_create_a_stock_adjustment()
    {
        // Persiapan (Arrange)
        // Pastikan kita memiliki kategori produk untuk diuji.
        $category = ProductCategory::factory()->create();
        
        // Buat file dummy untuk diunggah.
        Storage::fake('public');
        $image = UploadedFile::fake()->image('product.jpg');

        $requestData = [
            'name' => 'Test Product',
            'sku_no' => 'SKU-001',
            'product_category_id' => $category->id,
            'quantity' => 10,
            'path_image' => $image,
        ];
        
        // Simulasi request POST.
        $request = \Illuminate\Http\Request::create('/v1/inventory-management/store', 'POST', $requestData);
        $request->files->set('path_image', $image);
        
        // Aksi (Act)
        // Panggil metode 'store' dari service.
        $response = $this->productService->store($request);
        
        // Verifikasi (Assert)
        // Pastikan produk baru telah tersimpan di database.
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'sku_no' => 'SKU-001',
            'quantity' => 10,
            'product_category_id' => $category->id,
            'created_by' => $this->user->id,
        ]);
        
        // Pastikan entri StockAdjustments juga dibuat.
        $product = Product::where('sku_no', 'SKU-001')->first();
        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'user_id' => $this->user->id,
            'adjustment_type' => 0, // 0 = Product created
            'quantity_adjusted' => 10,
        ]);

        // Verifikasi file gambar tersimpan.
        Storage::disk('public')->assertExists('inventories/' . $image->hashName());
    }

    /** @test */
    public function it_can_increase_stock_and_record_the_adjustment()
    {
        // Persiapan (Arrange)
        $product = Product::factory()->create([
            'quantity' => 5,
        ]);
        
        $requestData = [
            'product_id' => $product->id,
            'adjust_type' => 1, // 1 = Increase
            'quantity' => 5,
            'remarks' => 'Increased stock via test.',
            'po_number_increase' => 'PO-TEST-001',
        ];
        $request = \Illuminate\Http\Request::create('/v1/inventory-management/adjust-stock', 'POST', $requestData);

        // Aksi (Act)
        $response = $this->productService->adjustStock($request);
        
        // Verifikasi (Assert)
        // Muat ulang produk dari database untuk mendapatkan kuantitas terbaru.
        $product->refresh();
        
        // Pastikan kuantitas produk telah diperbarui dengan benar.
        $this->assertEquals(10, $product->quantity);
        
        // Pastikan entri penyesuaian stok baru telah dibuat.
        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'adjustment_type' => 1,
            'quantity_adjusted' => 5,
            'previous_quantity' => 5,
            'new_quantity' => 10,
            'po_number' => 'PO-TEST-001',
        ]);
        
        // Pastikan respons dari API adalah JSON yang sukses.
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['success' => true, 'message' => 'Stock adjustment saved successfully.']), $response->getContent());
    }
    
    /** @test */
    public function it_can_decrease_stock_and_record_the_adjustment()
    {
        // Persiapan (Arrange)
        $product = Product::factory()->create([
            'quantity' => 10,
        ]);
        
        $requestData = [
            'product_id' => $product->id,
            'adjust_type' => 2, // 2 = Decrease
            'quantity' => 3,
            'remarks' => 'Decreased stock via test.',
            'po_number_decrease' => 'PO-CLIENT-001',
        ];
        $request = \Illuminate\Http\Request::create('/v1/inventory-management/adjust-stock', 'POST', $requestData);

        // Aksi (Act)
        $response = $this->productService->adjustStock($request);
        
        // Verifikasi (Assert)
        $product->refresh();
        
        // Pastikan kuantitas produk telah diperbarui dengan benar.
        $this->assertEquals(7, $product->quantity);
        
        // Pastikan entri penyesuaian stok baru telah dibuat.
        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'adjustment_type' => 2,
            'quantity_adjusted' => -3, // Nilai negatif
            'previous_quantity' => 10,
            'new_quantity' => 7,
            'po_number' => 'PO-CLIENT-001',
        ]);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['success' => true, 'message' => 'Stock adjustment saved successfully.']), $response->getContent());
    }
    
    /** @test */
    public function it_prevents_decreasing_stock_below_zero()
    {
        // Persiapan (Arrange)
        $product = Product::factory()->create([
            'quantity' => 5,
        ]);
        
        $requestData = [
            'product_id' => $product->id,
            'adjust_type' => 2, // 2 = Decrease
            'quantity' => 10, // Kuantitas melebihi stok yang ada
            'remarks' => 'Attempt to decrease below zero.',
        ];
        $request = \Illuminate\Http\Request::create('/v1/inventory-management/adjust-stock', 'POST', $requestData);

        // Aksi (Act)
        $response = $this->productService->adjustStock($request);

        // Verifikasi (Assert)
        $product->refresh();
        
        // Pastikan kuantitas produk tidak berubah.
        $this->assertEquals(5, $product->quantity);
        
        // Pastikan tidak ada entri penyesuaian stok baru yang dibuat.
        $this->assertDatabaseCount('stock_adjustments', 0);
        
        // Pastikan responsnya adalah JSON error dengan status 500.
        $this->assertEquals(500, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertStringContainsString('Quantity to decrease is more than current stock.', $responseData['message']);
    }
}
