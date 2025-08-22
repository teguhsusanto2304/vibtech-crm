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
                <div class="row align-items-end">
                    <div class="col-3 mb-3">
                        <label for="monthFilter" class="form-label">Filter by Month:</label>
                        <select id="monthFilter" class="form-select">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $i == now()->month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-2 mb-3">
                        <label for="yearFilter" class="form-label">Filter by Year:</label>
                        <input type="number" id="yearFilter" 
                            class="form-control" 
                            max="{{ date('Y') }}" 
                            value="{{ date('Y') }}">
                    </div>

                    <!-- Button aligned right -->
                    <div class="col mb-3 text-end">
                        <a href="{{ route('v1.receiving-order.create') }}" class="btn btn-success">
                            + Receive Order
                        </a>
                    </div>
                </div>

                
                <table id="receivingOrdersTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Received Date</th>
                            <th>Purchase Date</th>
                            <th>Created By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
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
        let table = $('#receivingOrdersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('v1.receiving-order.data') }}",
                data: function (d) {
                    d.month = $('#monthFilter').val(); 
                    d.year = $('#yearFilter').val(); 
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'po_number', name: 'po_number' },
                { data: 'supplier_name', name: 'supplier_name' },
                { data: 'receivedAt', name: 'receivedAt' },
                { data: 'purchaseAt', name: 'purchaseAt' },
                { data: 'createdBy', name: 'createdBy.name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // 🔄 Refresh table when filters change
        $('#supplierFilter, #monthFilter, #yearFilter').on('change', function () {
            table.ajax.reload();
        });
    });
   </script>
   <!-- Modal Detail Produk -->
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productDetailModalLabel">Product Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
    });
});
</script>
@endsection
