@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom-icon">
                    @foreach ($breadcrumb as $item)
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);">{{ $item }}</a>
                            <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                        </li>
                    @endforeach
                </ol>
            </nav>

            <h3>{{ $title }}</h3>

            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('errors'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('errors') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        <div id="msg"></div>
        <!-- DataTable Dependencies -->


        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
        <style>
            .action-col {
    width: 120px;
    min-width: 120px;
    max-width: 120px;
    white-space: nowrap; /* Keep buttons in one line */
}

        </style>

        <!-- Card -->
        <div class="card">
            <div class="card-header text-white d-flex flex-wrap justify-content-between align-items-center">
                <div></div>
               
            </div>
            <div class="card-body" style="overflow-x: auto;">
                <div class="mb-3">
                    <label for="categoryFilter" class="form-label">Filter by Category:</label>
                    <select id="categoryFilter" class="form-select w-25">
                        <option value="">All Categories</option>
                        {{-- Loop untuk menampilkan kategori dari database Anda --}}
                        @foreach(\App\Models\ProductCategory::WhereNot('data_status',0)->get() as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <table id="products-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>First Created At</th>
                            <th>First Created By</th>
                            <th>Last Updated At</th>
                            <th>Last Updated By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimasukkan di sini oleh DataTables -->
                    </tbody>
                </table>
            </div>
        <!-- Generic Confirmation Modal -->
            <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="confirmationModalBody">
                            Are you sure you want to proceed with this action?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmActionBtn">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- The Stock Adjustment Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-labelledby="adjustStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="adjustStockForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="adjustStockModalLabel">Adjust Stock for <span id="productName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="productIdInput">
                    <p class="mb-3">Current Stock: <span id="currentStockDisplay"></span></p>

                    <div class="mb-3">
                        <label for="adjust_type" class="form-label">Adjustment Type</label>
                        <select class="form-select" id="adjust_type" name="adjust_type" required>
                            <option value="1">Increase Stock</option>
                            <option value="2">Decrease Stock</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                    </div>

                    <!-- Increase Stock Fields -->
                    <div id="increaseFields" class="d-none">
                        <div class="mb-3">
                            <label for="po_number_increase" class="form-label">PO Number</label>
                            <input type="text" class="form-control" id="po_number_increase" name="po_number_increase" >
                        </div>
                        <div class="mb-3">
                            <label for="source" class="form-label">From (Company)</label>
                            <input type="text" class="form-control" id="source" name="source">
                        </div>
                        <div class="mb-3">
                            <label for="purchase_date" class="form-label">Purchase Date</label>
                            <input type="date" class="form-control" id="purchase_date" name="purchase_date" >
                        </div>
                        <div class="mb-3">
                            <label for="receive_date" class="form-label">Receive Date</label>
                            <input type="date" class="form-control" id="receive_date" name="receive_date" >
                        </div>
                    </div>

                    <!-- Decrease Stock Fields -->
                    <div id="decreaseFields" class="d-none">
                        <div class="mb-3">
                            <label for="po_number_decrease" class="form-label">PO Number</label>
                            <input type="text" class="form-control" id="po_number_decrease" name="po_number_decrease" >
                        </div>
                        <div class="mb-3">
                            <label for="draw_out_date" class="form-label">Product Draw Out Date</label>
                            <input type="date" class="form-control" id="draw_out_date" name="draw_out_date" >
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="product_id" id="product_id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveAdjustmentBtn">Update Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    document.addEventListener('DOMContentLoaded', function () {
    const adjustTypeSelect = document.getElementById('adjust_type');
    const increaseFields = document.getElementById('increaseFields');
    const decreaseFields = document.getElementById('decreaseFields');
    const quantityInput = document.getElementById('quantity');
    
    // Dapatkan semua input di dalam fieldset "Increase Stock"
    const increaseInputs = increaseFields.querySelectorAll('input, select, textarea');
    
    // Dapatkan semua input di dalam fieldset "Decrease Stock"
    const decreaseInputs = decreaseFields.querySelectorAll('input, select, textarea');

    function toggleFields() {
        const selectedValue = adjustTypeSelect.value;
        
        // Atur quantity sebagai required setiap saat
        quantityInput.setAttribute('required', 'required');

        if (selectedValue === '1') { // Increase Stock
            increaseFields.classList.remove('d-none');
            decreaseFields.classList.add('d-none');

            // Tambahkan atribut 'required' untuk input Increase
            increaseInputs.forEach(input => {
                input.setAttribute('required', 'required');
            });
            // Hapus atribut 'required' untuk input Decrease
            decreaseInputs.forEach(input => {
                input.removeAttribute('required');
            });

        } else if (selectedValue === '2') { // Decrease Stock
            increaseFields.classList.add('d-none');
            decreaseFields.classList.remove('d-none');

            // Tambahkan atribut 'required' untuk input Decrease
            decreaseInputs.forEach(input => {
                input.setAttribute('required', 'required');
            });
            // Hapus atribut 'required' untuk input Increase
            increaseInputs.forEach(input => {
                input.removeAttribute('required');
            });

        } else {
            // Jika tidak ada yang dipilih, hapus semua atribut 'required'
            increaseInputs.forEach(input => {
                input.removeAttribute('required');
            });
            decreaseInputs.forEach(input => {
                input.removeAttribute('required');
            });
        }
    }

    // Panggil fungsi saat halaman dimuat untuk menetapkan status awal
    toggleFields();

    // Tambahkan event listener untuk mendeteksi perubahan pada dropdown
    adjustTypeSelect.addEventListener('change', toggleFields);

    // Asumsi Anda telah menginisialisasi Datatables dengan ID 'productTable'
    // const productTable = $('#productTable').DataTable();
    
    const adjustStockModal = document.getElementById('adjustStockModal');
    const adjustStockForm = document.getElementById('adjustStockForm');
    const csrfToken = document.querySelector('input[name="_token"]').value;
    const adjustProductId = document.getElementById('product_id');

    adjustStockModal.addEventListener('show.bs.modal', event => {
        adjustStockForm.reset();
        const button = event.relatedTarget;
        const productId = button.getAttribute('data-product-id');
        const productName = button.getAttribute('data-product-name');
        const currentStock = button.getAttribute('data-current-stock');
        adjustProductId.value = productId;

        const modalTitle = adjustStockModal.querySelector('#productName');
        const productIdInput = adjustStockModal.querySelector('#productIdInput');
        const currentStockDisplay = adjustStockModal.querySelector('#currentStockDisplay');
        
        modalTitle.textContent = productName;
        productIdInput.value = productId;
        currentStockDisplay.textContent = currentStock;
        toggleFields();
    });




    // Event listener untuk submit form
    adjustStockForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Mencegah form untuk submit secara default
        
        const formData = new FormData(this);
        const adjustType = formData.get('adjust_type');

        // Mengatur nama input sesuai dengan jenis penyesuaian
        if (adjustType === '1') { // Increase Stock
            formData.append('po_number', formData.get('po_number_increase'));
        } else if (adjustType === '2') { // Decrease Stock
            formData.append('po_number', formData.get('po_number_decrease'));
        }
        
        // Mengirim data ke server
        fetch('/v1/inventory-management/stock-adjustment', { 
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
            },
            body: formData
        })
        .then(response => {
             if (!response.ok) {
                // Jika respons tidak sukses, lemparkan error untuk ditangkap di catch()
                return response.json().then(errorData => Promise.reject(errorData));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Tampilkan pesan sukses dan tutup modal
                alert(data.message); 
                    const modalElement = document.getElementById('adjustStockModal');
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalElement);
                    }
                    modalInstance.hide(); // Menggunakan instance yang dideklarasikan di atas
                
                // Perbarui tabel DataTables
                $('#products-table').DataTable().ajax.reload();
            } else {
                // Tampilkan pesan error dari server
                alert(data.message || 'Failed to save data.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Tampilkan pesan error yang lebih informatif
            alert(error.message || 'An error occurred. Please try again.');
        });
    });

    // Event listener untuk membersihkan form saat modal ditutup
    adjustStockModalElement.addEventListener('hidden.bs.modal', function () {
        adjustStockForm.reset();
    });
});

    
     $(document).ready(function () {
      var table =  $('#products-table').DataTable({
           processing: true,
           serverSide: true,
           scrollX: true,
           ajax: {
                url: "{{ route('v1.inventory-management.list.data') }}",
                // Tambahkan data filter ke dalam permintaan AJAX
                data: function (d) {
                    d.category_id = $('#categoryFilter').val();
                }
            },
           columns: [
                    { data: 'product_image', name: 'product_image', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'sku_no', name: 'sku_no' },
                    // Gunakan 'category_name' dari controller
                    { data: 'category_name', name: 'category_name' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'createdAt', name: "createdAt" },
                    { data: 'createdBy', name: 'createdBy' },
                    { data: 'updatedAt', name: 'updatedAt' },
                    { data: 'updatedBy', name: 'updatedBy' },
                    // Gunakan 'aksi' dari controller
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'action-col' }
                ]
       });

        $('#categoryFilter').on('change', function () {
            table.ajax.reload();
        });

     });
   </script>
   <!-- Modal Detail Produk -->
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productDetailModalLabel">Product Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="detail-tab" data-bs-toggle="tab" data-bs-target="#product-detail" type="button" role="tab" aria-controls="product-detail" aria-selected="true">
                            Product Detail
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#product-history" type="button" role="tab" aria-controls="product-history" aria-selected="false">
                            History
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="myTabContent">
                    <!-- Product Detail Tab Pane -->
                    <div class="tab-pane fade show active p-3" id="product-detail" data-bs-toggle="tab" role="tabpanel" aria-labelledby="detail-tab">
                        <div class="text-center mb-4">
                            <img id="productImage" src="" alt="Product Image" class="img-fluid rounded-lg shadow-sm" style="max-height: 200px;">
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Product Name:</span>
                                <span id="productNameDetail"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-bold">SKU:</span>
                                <span id="productSku"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Quantity:</span>
                                <span id="productQuantity"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Category:</span>
                                <span id="productCategory"></span>
                            </li>
                        </ul>
                    </div>

                    <!-- History Tab Pane -->
                    <div class="tab-pane fade p-3" id="product-history" role="tabpanel" aria-labelledby="history-tab">
    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-striped table-bordered table-sm w-100" id="historyTable">
                <thead>
                    <tr>
                        <th class="text-nowrap" style="width: 15%;">Date</th>
                        <th style="width: 70%;">History</th>
                        <th class="text-nowrap" style="width: 15%;">Staff</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan diisi oleh DataTables -->
                </tbody>
            </table>
    </div>
</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Tangkap modal ketika terbuka
    const productDetailModal = document.getElementById('productDetailModal');

    productDetailModal.addEventListener('show.bs.modal', function (event) {
        // Dapatkan tombol yang memicu modal
        const button = event.relatedTarget;
        // Ambil ID produk dari atribut data-id
        const productId = button.getAttribute('data-id');
        // Lakukan panggilan AJAX untuk mengambil data produk
        fetch(`/v1/inventory-management/${productId}/detail`) // Sesuaikan dengan endpoint API Anda
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Isi modal dengan data yang diterima
                document.getElementById('productNameDetail').textContent = data.name;
                document.getElementById('productSku').textContent = data.sku_no;
                document.getElementById('productQuantity').textContent = data.quantity;
                document.getElementById('productCategory').textContent = data.product_category.name;

                // Tampilkan gambar jika ada
                const productImage = document.getElementById('productImage');
                if (data.image) {
                    productImage.src = `/storage/${data.image}`; // Sesuaikan path jika perlu
                    productImage.classList.remove('d-none'); // Tampilkan gambar
                } else {
                    productImage.classList.add('d-none'); // Sembunyikan jika tidak ada gambar
                }
            })
            .catch(error => {
                console.error('Ada masalah saat mengambil data produk:', error);
                // Anda dapat menampilkan pesan kesalahan di sini
                document.getElementById('productName').textContent = 'Data tidak ditemukan.';
            });
    // Dapatkan elemen-elemen DOM
    if ($.fn.DataTable.isDataTable('#historyTable')) {
        $('#historyTable').DataTable().clear().destroy();
    }
    
    $('#historyTable').DataTable({
                processing: true,
                serverSide: true, // Gunakan pemrosesan sisi server
                ajax: {
                    url: `/v1/inventory-management/${productId}/history`, // Ganti dengan rute API Anda
                    type: "GET"
                },
                columns: [
                    // Kolom untuk 'Tanggal'
                    { data: 'date', name: 'created_at', orderable: false, searchable: false },
                    // Kolom untuk 'Riwayat'
                    { data: 'history', name: 'history'  },
                    // Kolom untuk 'Staf'
                    { data: 'staff', name: 'user', className: 'text-nowrap' }
                ],
                order: [[0, 'desc']],
            });  
    
    });
});
</script>
@endsection
