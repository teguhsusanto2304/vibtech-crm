<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('receiving_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->nullable()->comment('Nomor Pesanan Pembelian');
            //$table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->date('purchase_date')->default(now())->comment('Tanggal penerimaan barang');
            $table->date('received_date')->default(now())->comment('Tanggal penerimaan barang');
            $table->string('supplier_name',100)->comment('Nama penerima');
            $table->text('remarks')->nullable()->comment('Catatan tambahan');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receiving_orders');
    }
};
