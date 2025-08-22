@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- custom-icon Breadcrumb-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom-icon">
                    @foreach ($breadcrumb as $item)
                        <li class="breadcrumb-item">
                            @if($item == 'Job Assignment Form')
                                <a href="{{ route('v1.job-assignment-form')}}">{{ $item }}</a>
                            @else
                                <a href="javascript:void(0);">{{ $item }}</a>
                            @endif
                            <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                        </li>
                    @endforeach
                </ol>
            </nav>

            <h3>{{ $title }}</h3>
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Oops! Something went wrong:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form action="{{ route('v1.receiving-order.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="po_number" class="form-label">PO Number</label>
                                <input type="text" class="form-control" id="po_number" name="po_number" value="{{ old('po_number') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="supplier_name" class="form-label">Supplier <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="purchase_date" class="form-label">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', now()->toDateString()) }}" required>
                            </div>                            
                            <div class="col-md-3 mb-3">
                                <label for="received_date" class="form-label">Received Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="received_date" name="received_date" value="{{ old('received_date', now()->toDateString()) }}" required>
                            </div>                            
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ old('notes') }}</textarea>
                        </div>

                        <h4 class="mb-3">Product Items <span class="text-danger">*</span></h4>
                        <div id="product-list">
                            {{-- Baris produk akan ditambahkan di sini oleh JavaScript --}}
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm mt-3" id="add-product-btn">Add Item Product</button>
                        
                        <div class="mt-4 text-end">
                            <a href="{{ route('v1.receiving-order.list') }}" class="btn btn-danger">Cancel</a>
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </form>
                    <template id="product-row-template">
        <div class="row mb-3 product-row align-items-end border-bottom pb-3">
            <div class="col-md-8">
                <label for="" class="form-label">Product</label>
                <select class="form-select product-select" name="items[0][product_id]" required>
                    <option value="">Choose Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="" class="form-label">Quantity</label>
                <input type="number" class="form-control product-quantity" name="items[0][quantity]" min="1" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-product-btn">Remove</button>
            </div>
        </div>
    </template>
    
    <script src="[https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js](https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js)"></script>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const productList = document.getElementById('product-list');
        const addProductBtn = document.getElementById('add-product-btn');
        const productRowTemplate = document.getElementById('product-row-template');
        let itemCounter = 0;

        // Fungsi untuk memperbarui indeks nama input
        function updateInputNames() {
            const rows = productList.querySelectorAll('.product-row');
            rows.forEach((row, index) => {
                row.querySelector('.product-select').name = `items[${index}][product_id]`;
                row.querySelector('.product-quantity').name = `items[${index}][quantity]`;
            });
        }

        // Tambahkan baris produk pertama saat halaman dimuat
        function addProductRow() {
            const newRow = productRowTemplate.content.cloneNode(true);
            productList.appendChild(newRow);
            updateInputNames();
        }
        
        // Panggil fungsi untuk menambahkan baris produk pertama
        addProductRow();

        // Tambahkan baris baru saat tombol "Tambah Produk" diklik
        addProductBtn.addEventListener('click', function () {
            addProductRow();
        });

        // Hapus baris produk saat tombol "Hapus" diklik
        productList.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-product-btn')) {
                // Pastikan tidak ada baris yang tersisa
                if (productList.querySelectorAll('.product-row').length > 1) {
                    e.target.closest('.product-row').remove();
                    updateInputNames();
                } else {
                    alert('You must have minimum 1 item product.');
                }
            }
        });
    });

    </script>

@endsection
