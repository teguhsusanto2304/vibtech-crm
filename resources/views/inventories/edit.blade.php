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
            <div class="card">
                <div class="card-body">
                    <form class="row g-3"
                        action="{{ route('v1.inventory-management.update', $product->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="col-md-8">
                            <label for="staffName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="staffName" class="form-label">SKU</label>
                            <input type="text" class="form-control" id="sku_no" name="sku_no" value="{{ old('sku_no', $product->sku_no) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label for="staffName" class="form-label">Category</label>
                            <div class="input-group">
                                <select class="form-select" id="product_category_id" name="product_category_id" required>
                                    <option value="" disabled selected>Choose Category</option>
                                    @foreach ($productCategories as $category)
                                        <option value="{{ $category->id }}" {{ $category->id == $product->product_category_id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                    ...
                                </button>
                            </div>
                        </div>

                        
                        <div class="col-md-6">
                            <label for="path_image" class="form-label">Product  Image</label>
                            <input type="file" class="form-control" id="path_image" name="path_image">
                            @if ($product->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="Gambar Produk Saat Ini" style="max-width: 150px; border-radius: 8px;">
                                </div>
                            @endif
                        </div>



                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('v1.inventory-management.list')}}" class="btn btn-warning">Cancel</a>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
    <!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Manage Categories</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
                <!-- Create New Category -->
                <form id="createCategoryForm" class="mb-3">
                    @csrf
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" placeholder="New category name" required>
                        <button type="submit" class="btn btn-success">Add</button>
                    </div>
                </form>

                <!-- Category List -->
                <table class="table table-bordered" id="categoryTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productCategories as $category)
                        <tr data-id="{{ $category->id }}">
                            <td class="category-name">{{ $category->name }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning editCategoryBtn">Edit</button>
                                <button class="btn btn-sm btn-danger deleteCategoryBtn">Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('product_category_id');
    const categoryTable = document.querySelector('#categoryTable tbody');

    // Create Category
    document.getElementById('createCategoryForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        let res = await fetch('/categories', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        });

        let data = await res.json();
        if (res.ok) {
            // Add to select & table
            categorySelect.insertAdjacentHTML('beforeend', `<option value="${data.id}">${data.name}</option>`);
            categoryTable.insertAdjacentHTML('beforeend', `
                <tr data-id="${data.id}">
                    <td class="category-name">${data.name}</td>
                    <td>
                        <button class="btn btn-sm btn-warning editCategoryBtn">Edit</button>
                        <button class="btn btn-sm btn-danger deleteCategoryBtn">Delete</button>
                    </td>
                </tr>
            `);
            this.reset();
        } else {
            alert('Failed to add category');
        }
    });

    // Edit Category
    categoryTable.addEventListener('click', async function(e) {
        if (e.target.classList.contains('editCategoryBtn')) {
            let row = e.target.closest('tr');
            let nameCell = row.querySelector('.category-name');
            let newName = prompt('Edit category name:', nameCell.textContent);
            if (newName) {
                let id = row.dataset.id;
                let res = await fetch(`/categories/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ name: newName })
                });
                if (res.ok) {
                    nameCell.textContent = newName;
                    categorySelect.querySelector(`option[value="${id}"]`).textContent = newName;
                }
            }
        }
    });

    // Delete Category
    categoryTable.addEventListener('click', async function(e) {
        if (e.target.classList.contains('deleteCategoryBtn')) {
            if (!confirm('Delete this category?')) return;
            let row = e.target.closest('tr');
            let id = row.dataset.id;

            let res = await fetch(`/categories/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });

            if (res.ok) {
                row.remove();
                categorySelect.querySelector(`option[value="${id}"]`).remove();
            }
        }
    });
});
</script>

@endsection
