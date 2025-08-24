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
                            <th>Last Update At</th>
                            <th>Last Update By</th>
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
                    
                    <div class="mb-3">
                        <label for="adjust_number" class="form-label">Adjustment Number (Optional)</label>
                        <input type="text" class="form-control" id="adjust_number" name="adjust_number">
                    </div>

                    <div class="mb-3">
                        <label for="for_or_from" class="form-label">For/From (Optional)</label>
                        <input type="text" class="form-control" id="for_or_from" name="for_or_from">
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason (Optional)</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="product_id" id="product_id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveAdjustmentBtn">Save Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
    });

    // Menambahkan event listener untuk submit form
    adjustStockForm.addEventListener('submit', async (event) => {
        // Mencegah form untuk submit secara default
        event.preventDefault();

        // Dapatkan data form
        const formData = new FormData(adjustStockForm);
        const productId = formData.get('product_id');
        const saveButton = document.getElementById('saveAdjustmentBtn');

        // Nonaktifkan tombol untuk mencegah klik ganda
        saveButton.disabled = true;
        saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

        try {
            const response = await fetch(`/v1/inventory-management/stock-adjustment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok) {
                alert('Stock adjustment saved successfully!');
                
                // Tutup modal
                const modalInstance = bootstrap.Modal.getInstance(adjustStockModal);
                modalInstance.hide();
                $('#products-table').DataTable().ajax.reload(null, false);
                
                // Auto-refresh Datatables
                // PENTING: Sesuaikan dengan ID atau variabel Datatables Anda
                // Contoh:
                // productTable.ajax.reload(null, false); // Reload data tanpa mereset pagination
                
            } else {
                // Tampilkan pesan error dari server
                let errorMessage = 'Failed to save stock adjustment.';
                if (result.errors) {
                    errorMessage += '\n' + Object.values(result.errors).flat().join('\n');
                }
                alert(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        } finally {
            // Aktifkan kembali tombol setelah permintaan selesai
            saveButton.disabled = false;
            saveButton.innerHTML = 'Save Adjustment';
        }
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
                    <div class="tab-pane fade show active p-3" id="product-detail" role="tabpanel" aria-labelledby="detail-tab">
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
        <table class="table table-striped table-bordered table-sm">
            <thead>
                <tr>
                    <th class="text-nowrap" style="width: 15%;">Date</th>
                    <th style="width: 70%;">History</th>
                    <th class="text-nowrap" style="width: 15%;">Staff</th>
                </tr>
            </thead>
            <tbody id="historyTableBody">
                <!-- Data histori akan dimuat di sini oleh JavaScript -->
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
    const historyTableBody = document.getElementById('historyTableBody');
    
    // Tampilkan loading state
    historyTableBody.innerHTML = '<tr><td colspan="3" class="text-center">Loading...</td></tr>';

    if (productId) {
        fetch(`/v1/inventory-management/${productId}/history`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const historyData = data.data;
                historyTableBody.innerHTML = ''; // Kosongkan tabel sebelum mengisi

                if (historyData.length > 0) {
                    historyData.forEach(item => {
                        const formattedDate = new Date(item.created_at).toLocaleDateString('en-GB');
                        let adjust_type_name ='';
                        if(item.adjust_type==1){
                            adjust_type_name='Increase Stock';
                            productTotal = item.previous_quantity+item.quantity; 
                        } else {
                            adjust_type_name='Decrease Stock';
                            productTotal = item.previous_quantity-item.quantity; 
                        }
                        
                        // Membangun konten untuk kolom History
                        let historyContent = `
                            <strong>Adjustment Type:</strong> ${adjust_type_name}<br>
                            <strong>Previous Product Total:</strong> ${item.previous_quantity}<br>
                            <strong>Adjustment:</strong> ${item.quantity > 0 ? '+' : ''}${item.quantity}<br>
                            <strong>New Product Total:</strong> ${productTotal}<br>
                            <strong>Remarks:</strong> ${item.reason ?? 'N/A'}<br>
                        `;

                        // Tambahkan detail spesifik jika adjustment_type adalah 'Increase Stock'
                        if (item.adjust_type === 1) {
                            historyContent += `
                                <strong>PO Number:</strong> ${item.adjust_number ?? 'N/A'}<br>
                                <strong>From:</strong> ${item.for_or_from ?? 'N/A'}<br>
                                <strong>Product Purchased Date:</strong> ${item.updated_at ?? 'N/A'}<br>
                                <strong>Product Received Date:</strong> ${item.created_at ?? 'N/A'}<br>
                            `;
                        } else if (item.adjust_type === 2) {
                            historyContent += `
                                <strong>PO Number (Client):</strong> ${item.adjust_number ?? 'N/A'}<br>
                                <strong>Product Draw Out Date:</strong> ${item.created_at ?? 'N/A'}<br>
                            `;
                        } 
                        
                        const newRow = `
                            <tr>
                                <td class="text-nowrap">${formattedDate}</td>
                                <td>${historyContent}</td>
                                <td class="text-nowrap">${item.user.name ?? 'N/A'}</td>
                            </tr>
                        `;
        
                        historyTableBody.insertAdjacentHTML('beforeend', newRow);
                    });
                } else {
                    historyTableBody.innerHTML = '<tr><td colspan="3" class="text-center">No history found.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                historyTableBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Failed to load history.</td></tr>';
            });
    }
    });
});
    const productDetailModal = document.getElementById('productDetailModal');
productDetailModal.addEventListener('shown.bs.modal', function (event) {
    // Dapatkan ID produk dari tombol yang memicu modal
    const productId = event.relatedTarget.getAttribute('data-product-id');

    // Dapatkan elemen-elemen DOM
    const historyTableBody = document.getElementById('historyTableBody');
    
    // Tampilkan loading state
    historyTableBody.innerHTML = '<tr><td colspan="3" class="text-center">Loading...</td></tr>';

    if (productId) {
        fetch(`/v1/inventory-management/${productId}/history`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const historyData = data.data;
                historyTableBody.innerHTML = ''; // Kosongkan tabel sebelum mengisi

                if (historyData.length > 0) {
                    historyData.forEach(item => {
                        const formattedDate = new Date(item.created_at).toLocaleDateString('en-GB');
                        
                        // Membangun konten untuk kolom History
                        let historyContent = `
                            <strong>Adjustment Type:</strong> ${item.adjustment_type}<br>
                            <strong>Previous Product Total:</strong> ${item.previous_quantity}<br>
                            <strong>Adjustment:</strong> ${item.adjusted_quantity > 0 ? '+' : ''}${item.adjusted_quantity}<br>
                            <strong>New Product Total:</strong> ${item.new_quantity}<br>
                            <strong>Notes:</strong> ${item.notes ?? 'N/A'}<br>
                        `;

                        // Tambahkan detail spesifik jika adjustment_type adalah 'Increase Stock'
                        if (item.adjustment_type === 'Increase Stock') {
                            historyContent += `
                                <strong>PO Number:</strong> ${item.po_number ?? 'N/A'}<br>
                                <strong>From:</strong> ${item.source ?? 'N/A'}<br>
                                <strong>Product Purchased Date:</strong> ${item.purchase_date ?? 'N/A'}<br>
                                <strong>Product Received Date:</strong> ${item.received_date ?? 'N/A'}<br>
                            `;
                        } else if (item.adjustment_type === 'Decrease Stock') {
                            historyContent += `
                                <strong>PO Number (Client):</strong> ${item.po_number ?? 'N/A'}<br>
                                <strong>Product Draw Out Date:</strong> ${item.draw_out_date ?? 'N/A'}<br>
                            `;
                        }
                        
                        const newRow = `
                            <tr>
                                <td class="text-nowrap">${formattedDate}</td>
                                <td>${historyContent}</td>
                                <td class="text-nowrap">${item.created_by.name ?? 'N/A'}</td>
                            </tr>
                        `;
        
                        historyTableBody.insertAdjacentHTML('beforeend', newRow);
                    });
                } else {
                    historyTableBody.innerHTML = '<tr><td colspan="3" class="text-center">No history found.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                historyTableBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Failed to load history.</td></tr>';
            });
    }
    });
</script>
@endsection
