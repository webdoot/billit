@extends('layouts.app')

@section('title', 'Service Products')
@section('page_title', 'Service Products Catalogue')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-box-open text-primary me-2"></i>Service Products</h5>
                @can('services.create')
                <a href="{{ route('service-products.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus me-1"></i> Add Product
                </a>
                @endcan
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="products-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Product Name</th>
                                <th>Billing Cycle</th>
                                <th>Standard Price (₹)</th>
                                <th>Status</th>
                                <th style="width: 150px; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td><span class="badge bg-light text-dark border">{{ $product->category->name ?? 'N/A' }}</span></td>
                                    <td class="fw-bold text-dark">{{ $product->name }}</td>
                                    <td>{{ $product->billing_cycle }}</td>
                                    <td class="fw-semibold">₹{{ number_format($product->price, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $product->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $product->status }}
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        @can('services.edit')
                                        <a href="{{ route('service-products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fa-regular fa-edit" title="Edit"></i>
                                        </a>
                                        @endcan

                                        @can('services.delete')
                                        <form action="{{ route('service-products.destroy', $product->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-delete">
                                                <i class="fa-regular fa-trash-can" title="Delete"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary">No products configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#products-table').DataTable({
            order: [[0, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search products..."
            }
        });

        // SweetAlert Delete Confirmation
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting this product will remove it permanently from the catalogue.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
