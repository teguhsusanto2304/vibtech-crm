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
                <table id="products-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Created At</th>
                            <th>Created By</th>
                            <th>Last Update</th>
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
            <form action="{{ route('stock.adjust') }}" method="POST">
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
                            <option value="increase">Increase Stock</option>
                            <option value="decrease">Decrease Stock</option>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const adjustStockModal = document.getElementById('adjustStockModal');
    adjustStockModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const productId = button.getAttribute('data-product-id');
        const productName = button.getAttribute('data-product-name');
        const currentStock = button.getAttribute('data-current-stock');

        // Update the modal's content
        const modalTitle = adjustStockModal.querySelector('.modal-title #productName');
        const productIdInput = adjustStockModal.querySelector('#productIdInput');
        const currentStockDisplay = adjustStockModal.querySelector('#currentStockDisplay');
        
        modalTitle.textContent = productName;
        productIdInput.value = productId;
        currentStockDisplay.textContent = currentStock;
    });
</script>
            <script type="text/javascript">
     $(document).ready(function () {
      var table =  $('#products-table').DataTable({
           processing: true,
           serverSide: true,
           scrollX: true,
           ajax: "{{ route('v1.inventory-management.list.data') }}",
           columns: [
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'sku_no', name: 'sku_no' },
                    // Gunakan 'category_name' dari controller
                    { data: 'category_name', name: 'category_name' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'createdAt', name: "createdAt" },
                    { data: 'created_by', name: 'created_by' },
                    { data: 'updatedAt', name: 'updatedAt' },
                    // Gunakan 'aksi' dari controller
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'action-col' }
                ]
       });

     });
   </script>
@endsection
